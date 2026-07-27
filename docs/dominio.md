# Dominio del negocio — Backend

## Arquitectura multi-tenant

| Contexto | Dominio | Rutas | Para qué |
|---|---|---|---|
| **Central** | dominio raíz | `routes/api.php` | Gestión de tenants, planes SaaS, super admin |
| **Tenant** | subdominio | `routes/tenant.php` | Operaciones de la inmobiliaria: propiedades, personas, empresa, etc. |

El middleware `check.subscription` protege todos los endpoints de tenant y valida que tenga un plan activo.

---

## Catálogo de módulos

> Archivos clave en `app/` — omitir prefijo `app/`. Ver catálogo completo en el CLAUDE.md raíz.

| Módulo | Estado | Archivos clave (Controlador · Servicio · Modelo) |
|---|---|---|
| **Lookup** | ✅ | `LookupController` · `LookupService` · `Models/Lookup` |
| **Person** | ✅ | `PersonController` · `PersonService` · `Models/Person` + `FiscalProfile` + `AccountBank` |
| **Property** | ✅ | `PropertyController` · `PropertyService` · `Models/Property` + sub-modelos |
| **Company** | ✅ | `CompanyController` · `CompanyService` · `Models/Company` |
| **FiscalProfile** | ✅ | Gestionado por `PersonService`/`CompanyService` · `Models/FiscalProfile` |
| **User** | ✅ | `UserController` · `UserService` · `Models/User` |
| **Role + Permission** | ✅ | `RoleController` · `RoleService` · (Spatie models) |
| **Image** | ✅ | `ImageController` · polimórfica (Property galería, Company logo) |
| **Audit** | ✅ | `AuditController` · `AuditService` · `Support/AuditValueResolver` · ver `auditoria.md` |
| **RealstateSite** | ✅ | `RealstateTemplateManagementController` · `RealstateTemplateManagementService` |
| **Plan** (central) | ✅ | `PlanController` · `PlanService` |
| **Tenant** (central) | ✅ | `TenantController` · `TenantService` |
| **Rent** | ✅ | `RentController` · `RentService` · `Models/Rent` + `RentObligation` + `RentTenantCodebtor` + `Liability` |
| **ReportTemplate** | ✅ | `ReportTemplateController` · `ReportTemplateService` · `Models/ReportTemplate` |
| **Document** | ✅ | `TemplateSectionController` · `DocumentController` · `Models/Document` · ver `dominio-documentos.md` |
| **LeaseFee** | 📋 | Modelo vacío — módulo futuro |
| **Warranty** | 📋 | Modelo vacío — módulo futuro |
| **Branch** | 📋 | Sucursales — pendiente. Ver `dominio-sucursales.md` |

---

## Mapa de entidades y relaciones

```
Company ──── FiscalProfile
   │
   ├── Person (legalRepresentative)
   ├── Person (personAttendant)
   ├── contacts[] (Contact)
   ├── addresses[] (Address)
   └── logo (Image — morphOne)

Person ──── FiscalProfile
   │
   ├── contacts[] (Contact)
   ├── addresses[] (Address)
   ├── accountBanks[] (AccountBank)
   ├── User (opcional — acceso al sistema)
   └── properties[] (pivot property_person)

Property
   │
   ├── owners[] (Person via pivot property_person)
   │       pivot: ownership_percentage, is_primary_owner, fechas
   ├── areas[] (PropertyArea)
   ├── price (PropertyPrice)
   ├── features[] (PropertyFeature)
   ├── obligations[] (PropertyObligation)
   ├── publishChannels[] (PublishChannel)
   └── images[] (Image — morphMany)

Rent
   ├── property (Property)
   ├── tenants[] (Person via pivot rent_tenant_codebtor)
   ├── codebtors[] (Person via pivot rent_tenant_codebtor)
   ├── rentObligations[] (RentObligation)
   ├── liabilities[] (Liability)
   └── documents[] (Document — morphMany)
```

---

## Módulo Lookup (catálogos)

Tabla única de valores clasificados por `category` (string). No hay tablas separadas por tipo.

**Categorías de uso frecuente:**

| category | Usado en |
|---|---|
| `country`, `department`, `city` | Address |
| `document_type` | Person |
| `organization_type`, `gender_type` | Person |
| `property_type`, `offer_type` | Property |
| `status` (inmueble), `status_property` | Property |
| `stratum`, `garage_type` | Property |
| `area_type`, `area_unit` | PropertyArea |
| `price_type` | PropertyPrice |
| `feature_type` | PropertyFeature |
| `obligation_type`, `frequency_type` | PropertyObligation, RentObligation |
| `channel` | PublishChannel |
| `account_type`, `bank` | AccountBank |
| `via_type`, `letra1`, `letra2`, `orientation1`, `orientation2` | Address (nomenclatura vial colombiana) |
| `responsible_for_vat_type` | FiscalProfile |
| `contract_type`, `increment_type`, `payment_bank`, `liability_type` | Rent, RentObligation, Liability |

---

## Módulo Person (personas)

Personas naturales (first_name + last_name) y jurídicas (company_name). El campo `organization_type_id` determina el tipo.

**Campos calculados automáticamente:**
- `dv` — dígito de verificación al asignar `document_number` → `CalculateDV::fromNumber()`
- `document_type_alias`, `organization_type_alias` — appended attributes

**Sub-entidades sincronizadas** (todas en `LogBatch` + transacción):
- `contacts[]`, `addresses[]`, `accountBanks[]`
- Via `FiscalProfile`: `taxeTypes[]`, `economicActivities[]`

**Roles de una Person en el sistema:**
- Propietario de propiedad (pivot `property_person`)
- Representante legal / persona encargada de Company
- Arrendatario / Codeudor de Rent (pivot `rent_tenant_codebtor`)
- Puede tener un User asociado

---

## Módulo Property (propiedades)

**Código secuencial:** `PROP-000001` con `lockForUpdate` → `Property::generateSequentialCode()`.

**Dos estados diferenciados:**
- `status_id` — publicación (borrador, publicado, pausado, archivado)
- `status_property_id` — estado físico (disponible, arrendado, en venta, vendido)

**syncHasMany:** maneja upsert/restore/delete de sub-entidades. Para que los eventos Eloquent `deleted` se disparen (necesario para auditoría), usar `->get()->each->delete()` en lugar de `->delete()` directo via query builder. Ver `auditoria.md`.

---

## Módulo FiscalProfile (perfil fiscal)

Compartido entre Person y Company. Configuración tributaria colombiana:
- `tax_regime`, `responsible_for_vat_type_id`, `vat_withholding`, `income_tax_withholding`, `ica_withholding`, `rental_fee`
- `taxeTypes[]` — tipos de impuesto (CIIU)
- `economicActivities[]` — actividades económicas

---

## Módulo Address (dirección)

FKs explícitas (no polimórfica): `person_id`, `company_id`, `property_id`. Nomenclatura vial colombiana: `via_type_id`, `via_number`, `letra1_id`, `orientation1_id`, `number2`, `letra2_id`, `orientation2_id`, `number3` → todos apuntan a Lookups.

---

## Módulo Rent (contratos)

**Estado: ✅ implementado.** CRUD completo + auditoría con sub-modelos en batch.

**Sub-modelos auditados bajo `log_name='rents'`:** `RentObligation`, `RentTenantCodebtor`, `Liability`.

**Pivote `rent_tenant_codebtor`:** un par tenant-codeudor por fila. 1 tenant + 2 codeudores → 2 filas.

**Flujo de negocio (diagrama Veltra):**
```
1. Registro de Terceros (Person) — propietarios, arrendatarios, codeudores
2. Registro de Propiedades (Property)
3. Tipo de contrato: Arrendamiento / Comodato / Colocación
4. Condiciones del contrato:
   - Propiedad + propietarios (% participación)
   - Fechas, canon, período, destinación, actividad
   - Impuestos, incremento IPC, deducciones, comisión
   - Cláusulas adicionales, documentos
5. Garantías (póliza de seguro, codeudores)
6. Comisión inmobiliaria (% pactado con propietario)
7. Firma y almacenamiento del contrato
8. Facturación y pagos al propietario (LeaseFee — módulo futuro)
```

---

## Módulo ReportTemplate (informes)

**Estado: ✅ implementado.** Plantillas de columnas configurables para exportar datos de contratos a Excel.

- `columns` — array JSON de columnas seleccionadas por el usuario
- `is_default` — no se puede eliminar la plantilla predeterminada
- Auditoría bajo `log_name='reports'`
- `Support/ReportVariables` — catálogo de variables disponibles y resolución de valores por columna
