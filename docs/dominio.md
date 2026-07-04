# Dominio del negocio — Backend

## Qué es el sistema

SaaS multi-tenant para **inmobiliarias colombianas**. Cada tenant es una inmobiliaria que gestiona su portafolio de propiedades, personas (propietarios, arrendatarios, clientes), contratos de arriendo y la configuración de su sitio web público.

---

## Arquitectura multi-tenant

| Contexto | Dominio | Rutas | Para qué |
|---|---|---|---|
| **Central** | dominio raíz | `routes/api.php` | Gestión de tenants, planes SaaS, super admin |
| **Tenant** | subdominio | `routes/tenant.php` | Operaciones de la inmobiliaria: propiedades, personas, empresa, etc. |

El middleware `check.subscription` protege todos los endpoints de tenant y valida que tenga un plan activo.

---

## Catálogo de módulos

| Módulo | Estado | Descripción |
|---|---|---|
| **Lookup** | ✅ | Catálogos/desplegables por categoría |
| **Person** | ✅ | Personas naturales y jurídicas |
| **Property** | ✅ | Inmuebles con todo su portafolio de datos |
| **Company** | ✅ | Empresa inmobiliaria del tenant |
| **FiscalProfile** | ✅ | Perfil fiscal compartido por Person y Company |
| **User** | ✅ | Usuarios del sistema con roles/permisos |
| **Role + Permission** | ✅ | Control de acceso via Spatie |
| **Image** | ✅ | Imágenes polimórficas (Property, Company) |
| **Audit** | ✅ | Log de actividad via Spatie Activitylog |
| **RealstateSite** | ✅ | Configuración visual del sitio público |
| **Plan** | ✅ | Planes de suscripción SaaS (central) |
| **Tenant** | ✅ | Clientes del SaaS (central) |
| **Rent** | 🚧 | Contratos de arriendo — modelo creado, pendiente CRUD completo |
| **LeaseFee** | 📋 | Cuotas del arriendo — modelo vacío, módulo futuro |
| **Warranty** | 📋 | Garantías del arriendo — modelo vacío, módulo futuro |
| **Liability** | 📋 | Obligaciones del contrato — modelo vacío, módulo futuro |
| **Document** | 📋 | Documentos del arriendo — modelo vacío, módulo futuro |
| **LimitDate** | 📋 | Fechas límite del arriendo — modelo vacío, módulo futuro |

---

## Mapa de entidades y relaciones

```
Company ──── FiscalProfile
   │
   ├── Person (legalRepresentative)
   ├── Person (personAttendant)
   ├── contacts[] (Contact)
   ├── addresses[] (Address)
   ├── logo (Image — morphOne)
   └── setting (CompanySetting)

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
   ├── areas[] (PropertyArea — tipo + unidad + valor)
   ├── price (PropertyPrice)
   ├── features[] (PropertyFeature)
   ├── obligations[] (PropertyObligation — tipo, frecuencia, estado)
   ├── publishChannels[] (PublishChannel)
   ├── images[] (Image — morphMany, ordenadas por sort_order)
   ├── contacts[] (Contact)
   └── addresses[] (Address)

Rent ──── FiscalProfile
   │
   ├── tenants[] (Person via pivot rent_tenant_codebtor)
   └── codebtors[] (Person via pivot rent_tenant_codebtor)
       (mismo pivot — un tenant puede tener múltiples codeudores)
```

---

## Módulo Lookup (catálogos)

Tabla única de valores clasificados por `category` (string). No hay tablas separadas por tipo de dato.

**Categorías de uso frecuente:**

| category | Usado en |
|---|---|
| `country`, `department`, `city` | Address |
| `document_type` | Person |
| `organization_type` | Person |
| `gender_type` | Person |
| `property_type`, `offer_type` | Property |
| `status` (inmueble), `status_property` | Property |
| `stratum` | Property, Address |
| `garage_type` | Property |
| `area_type`, `area_unit` | PropertyArea |
| `price_type` | PropertyPrice |
| `feature_type` | PropertyFeature |
| `obligation_type`, `frequency_type` | PropertyObligation |
| `channel` | PublishChannel |
| `account_type`, `bank` | AccountBank |
| `via_type`, `letra1`, `letra2`, `orientation1`, `orientation2` | Address (estructura vial colombiana) |
| `responsible_for_vat_type` | FiscalProfile |

Al crear campos que dependen de catálogos, la FK apunta a `lookups.id` y la relación usa `BelongsTo(Lookup::class, '{campo}_id')`.

---

## Módulo Person (personas)

Representa tanto **personas naturales** (first_name + last_name) como **personas jurídicas** (company_name). El campo `organization_type_id` determina el tipo.

**Campos calculados automáticamente:**
- `dv` — dígito de verificación, se calcula al asignar `document_number` via Attribute mutator → `CalculateDV::fromNumber()`
- `document_type_alias`, `organization_type_alias` — appended attributes para facilitar listados

**Sub-entidades sincronizadas** (toda operación que las toque va en `LogBatch` + transacción):
- `contacts[]` — Contact (teléfono, email, etc.)
- `addresses[]` — Address (dirección con nomenclatura vial colombiana)
- `accountBanks[]` — AccountBank

**Roles de una Person en el sistema:**
- Propietario de propiedad (via `property_person`)
- Representante legal de Company (`legal_representative_id`)
- Persona encargada de Company (`person_attendant_id`)
- Arrendatario de Rent (via `rent_tenant_codebtor`)
- Codeudor de Rent (via `rent_tenant_codebtor`)
- Puede tener un User asociado para acceso al sistema

---

## Módulo Property (propiedades)

**Código secuencial:** `PROP-000001` generado con `lockForUpdate` en transacción para evitar duplicados en concurrencia. Ver `Property::generateSequentialCode()`.

**Dos estados diferenciados:**
- `status_id` — estado de publicación del inmueble (borrador, publicado, pausado, archivado)
- `status_property_id` — estado físico del inmueble (disponible, arrendado, en venta, vendido)

**Sub-entidades con `syncHasMany`:** El método `syncHasMany()` del modelo maneja upsert/restore/delete de sub-entidades. Soporta dos modos:
- Normal (por `id`): para areas, features, obligations, publishChannels, prices
- Clave compuesta: para ownerships (property_person)

**Canales de publicación (`PublishChannel`):** cada propiedad puede publicarse en múltiples portales (tipo canal via Lookup `channel`).

---

## Módulo FiscalProfile (perfil fiscal)

Entidad reutilizable compartida entre **Person** y **Company**. Contiene la configuración tributaria colombiana:

- `tax_regime` — régimen tributario
- `responsible_for_vat_type_id` → Lookup — responsable de IVA
- `vat_withholding`, `income_tax_withholding`, `ica_withholding` — retenciones (boolean)
- `rental_fee` — tarifa de arrendamiento
- `taxeTypes[]` — tipos de impuesto asociados
- `economicActivities[]` — actividades económicas (CIIU)

---

## Módulo Address (dirección)

Usa nomenclatura vial colombiana. No es polimórfica via morphs — tiene FKs explícitas:
- `person_id` — dirección de persona
- `company_id` — dirección de empresa
- `property_id` — ubicación del inmueble

Campos de nomenclatura: `via_type_id`, `via_number`, `letra1_id`, `orientation1_id`, `number2`, `letra2_id`, `orientation2_id`, `number3` → todos apuntan a Lookups de sus categorías respectivas.

---

## Módulo Rent (contratos de arriendo) — 🚧 en desarrollo

El modelo `Rent` existe con sus relaciones. Módulos secundarios (`LeaseFee`, `Warranty`, `Liability`, `Document`, `LimitDate`) tienen modelos vacíos — son el próximo ciclo de desarrollo.

**Pivote `rent_tenant_codebtor`:** una fila por par tenant-codeudor. Un arriendo con 1 tenant y 2 codeudores → 2 filas en el pivot.

---

## Patrones transversales del backend

### syncHasMany
Patrón en Person, Property y Company para sincronizar sub-entidades HasMany. Hace upsert (crea si no existe, restaura si fue soft-deleted, actualiza si existe), y elimina los que no vienen en el array.

### LogBatch
Cuando un service escribe **más de un modelo con `LogsActivity`**, envolver en `LogBatch::startBatch()/endBatch()` en el `finally`. Esto agrupa todos los logs bajo el mismo `batch_uuid` para que el módulo de auditoría los muestre como una sola operación.

### Image (polimórfica)
`Image` usa morphs reales (`imageable_type`, `imageable_id`). `Property` tiene `morphMany` (galería), `Company` tiene `morphOne` (logo). El endpoint `/api/images` recibe `imageable_type` y `imageable_id` para asociar.

### Permisos por módulo
Cada módulo tiene su conjunto de permisos en formato `{modulo}.{accion}` (ej: `properties.view`, `properties.create`). Ver docs de permisos en skill `laravel-permission-development`.
