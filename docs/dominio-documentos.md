# Módulo de Documentos — Plan de implementación

> **Estado**: En desarrollo activo  
> **Fecha**: 2026-07-12  
> **Prioridad actual**: Fase 5 (firma electrónica)
>
> **Fases completadas**: 3 (contratos), 4 (actas), 4b (otros tipos), 7 (plantillas editables)

---

## Regla crítica de migraciones

> **Estamos en desarrollo. NO crear nuevas migraciones para tablas que ya existen.**  
> Modificar directamente la migración existente correspondiente.
>
> | Tabla | Migración a modificar |
> |---|---|
> | `rents` | `2025_09_24_152653_create_rents_table.php` |
> | `documents` | `2025_09_18_225517_create_documents_table.php` |
> | `liabilities` | `2025_09_24_164738_create_liabilities_table.php` |
> | `properties` y sus tablas | `2025_09_03_210613_create_properties_table.php` |
>
> Solo para tablas completamente nuevas (que no existan en ninguna migración) se crea un archivo de migración nuevo.
> Tablas nuevas confirmadas hasta ahora: `rent_obligations`, `document_signatories`.

---

## Nota sobre FiscalProfile

Los campos `rental_fee`, `responsible_for_vat_type_id` y `tax_regime` de `FiscalProfile` están en la migración pero **ya no se usan** en el negocio. Ignorarlos completamente al implementar contratos y facturas. Las tasas de retención (`vat_withholding`, `income_tax_withholding`, `ica_withholding`) son valores de referencia por persona pero las tasas reales se definen en el contrato.

---

## 1. Visión del módulo

Un módulo **Document** centralizado y dinámico que permite generar, almacenar y gestionar cualquier tipo de documento inmobiliario (contratos, actas, facturas, pólizas, inventarios, preaviso, etc.) sin estar atado a tipos fijos. El tipo de documento determina su comportamiento, su plantilla PDF y sus campos específicos.

**Principio clave**: `Rent` es el registro de condiciones del contrato (quién, qué inmueble, a cuánto, por cuánto tiempo). `Document` es el PDF generado a partir de esas condiciones, más otros documentos del ciclo de vida del arrendamiento.

**Un `Document` = un archivo**. Cada registro en `documents` representa un archivo (PDF generado, PDF firmado subido, foto, escaneo). Documentos que tienen múltiples archivos (ej: contrato original + versión firmada) usan múltiples registros `Document` con `document_type` diferente, o un `parent_document_id` que los agrupe.

---

## 2. Tipos de documentos identificados (del negocio real)

| Categoría (`document_category`) | Tipo (`document_type`) | Descripción |
|---|---|---|
| `contrato` | `arrendamiento_vivienda` | Contrato Ley 820/2003 — residencial |
| `contrato` | `arrendamiento_comercial` | Contrato Decreto 410/1971 — local comercial |
| `contrato` | `administracion_mandato` | Contrato inmobiliaria ↔ propietario, incluye % comisión |
| `contrato` | `comodato` | Propietario cede uso gratuito a comodatario |
| `contrato` | `colocacion` | Inmobiliaria cobra solo por conseguir inquilino |
| `acta` | `entrega_inmueble` | Acta al inicio del contrato — estado del inmueble al recibir |
| `acta` | `devolucion_inmueble` | Acta al terminar el contrato — estado al devolver, deudas pendientes |
| `acta` | `inspeccion` | Visita de inspección periódica |
| `factura` | `canon` | Factura del canon mensual al arrendatario |
| `factura` | `liquidacion` | Liquidación final al terminar el contrato |
| `poliza` | `seguro_arrendamiento` | Póliza de aseguradora para garantizar el canon |
| `garantia` | `codeudor` | Información y condiciones del codeudor/coarrendatario |
| `inventario` | `inventario_inmueble` | Inventario detallado de elementos del inmueble |
| `preaviso` | `preaviso_terminacion` | Carta de preaviso de no renovación del contrato |

> Esta lista es extensible via Lookups — agregar un nuevo tipo no requiere código, solo insertar registros en la tabla `lookups`.

---

## 3. Lo que ya existe en el backend

| Elemento | Estado | Relevancia para Documents |
|---|---|---|
| `Rent` | 🚧 modelo creado, CRUD pendiente | **Padre directo** — el contrato se genera desde un Rent |
| `Property` | ✅ completo | Inmueble del contrato |
| `Person` | ✅ completo | Arrendatario, propietarios, codeudores |
| `Company` | ✅ completo | La inmobiliaria (arrendador en el PDF) |
| `Document` | 🚧 migración existe con estructura base, modelo vacío | **A completar** — modificar migración existente |
| `Warranty` | 📋 modelo vacío | Garantías del arriendo — vincular a Document |
| `LeaseFee` | 📋 modelo vacío | Cuotas — vinculadas a Document tipo factura |
| `Liability` | 📋 migración existe, modelo vacío | Responsabilidades del contrato (cuota admin, etc.) |
| `Lookup` | ✅ completo | Catálogos para categorías y tipos |

---

## 4. Estado real de la tabla `rents` y qué falta

La migración `create_rents_table` **ya tiene** más campos de lo que se asumía inicialmente:

### 4.1 Campos que YA existen en `rents`

| Campo en migración | Equivalente del PDF | Nota |
|---|---|---|
| `property_id` | Inmueble del contrato | ✅ FK a `properties` ya existe |
| `start_date` | Fecha de inicio | ✅ existe |
| `end_date` | Fecha de terminación | ✅ existe |
| `duration` | Duración en meses | ✅ existe (int) |
| `destination` | Destinación (vivienda/comercial) | ✅ existe como string — considerar migrar a FK Lookup |
| `activity` | Actividad específica permitida | ✅ existe |
| `period` | Período de pago | ✅ existe como date — revisar si es suficiente o si necesita `period_day` (int) |
| `interest_rate` | Incremento / tasa de interés | ✅ existe como string (permite "IPC+2") |
| `consignment_account` | Cuenta bancaria para consignar | ✅ existe |
| `commissions` | Comisión de administración | ✅ existe |
| `status` | Estado del contrato | ✅ existe |
| `limit_dates_id` | FK a limit_dates | ✅ existe |

### 4.2 Campos que FALTAN en `rents` (agregar a la migración existente)

| Campo | Tipo | Descripción | Del PDF |
|---|---|---|---|
| `contract_number` | string | Número del contrato (46952, 47031...) | Nº en la cabecera |
| `contract_type_id` | FK Lookup | `contract_type`: vivienda / comercial / comodato... | Título del contrato |
| `canon` | decimal(12,2) | Valor del canon neto | CANON |
| `iva` | decimal(12,2) | IVA (solo contratos comerciales con IVA) | IVA |
| `administration_included` | boolean | ¿Administración incluida en el canon? | ADMINISTRACIÓN |
| `is_ph` | boolean | ¿Sometido a propiedad horizontal? | SOMETIDO A P.H |
| `increment_type_id` | FK Lookup | `increment_type`: fijo / IPC / IPC+puntos | INCREMENTO |
| `adjustment_date` | date | Fecha de reajuste del canon (ej: 16 mayo cada año) | REAJUSTE DEL CANON |
| `is_insured` | boolean | ¿Contrato asegurado? | ASEGURADO |
| `payment_bank_id` | FK Lookup | Banco de la cuenta de consignación | BANCOLOMBIA, etc. |
| `signed_city` | string(100) | Ciudad donde se firma | "MEDELLÍN" |
| `signed_at` | date | Fecha de firma del contrato | Fecha firma |
| `additional_clauses` | JSON | Array de cláusulas adicionales especiales | CLÁUSULAS ADICIONALES |
| `internal_notes` | text | Notas internas de la inmobiliaria | — |

> **Campos a revisar antes de implementar**: `destination` (string → ¿FK Lookup?), `period` (date → ¿necesita `period_day` int adicional?). Verificar con el equipo qué formato se usó originalmente.

### 4.3 Lookups nuevos a crear (seeders, no migraciones)

| Categoría | Valores |
|---|---|
| `contract_type` | arrendamiento_vivienda, arrendamiento_comercial, administracion_mandato, comodato, colocacion |
| `increment_type` | porcentaje_fijo, ipc, ipc_mas_puntos |
| `document_category` | contrato, acta, factura, poliza, garantia, inventario, preaviso, otro |
| `document_type` | (ver sección 2) |
| `document_status` | borrador, generado, enviado, firmado, archivado, anulado |

---

## 5. Diseño del modelo `Document` (sobre la migración existente)

### 5.1 La migración existente ya tiene

La migración `2025_09_18_225517_create_documents_table.php` ya tiene:
- Polimórfico: `documentable_type / documentable_id`
- `document_type_id` → Lookup
- `title`, `description`
- `file_name`, `file_path`, `file_extension`, `mime_type`, `file_size`
- `document_date`, `expiry_date`
- `status_id` → Lookup
- `sort_order`, `is_public`, `is_verified`

### 5.2 Campos a AGREGAR en la migración existente de `documents`

| Campo | Tipo | Descripción |
|---|---|---|
| `document_category_id` | FK Lookup | Categoría: contrato / acta / factura / ... |
| `number` | string | Número del documento (para contratos: el contract_number del Rent) |
| `template_key` | string | Clave de la plantilla PDF (ej: `contract.rental.residential`) |
| `content` | JSON | Campos específicos del tipo de documento (ver 5.3) |
| `generated_at` | timestamp | Cuándo se generó el PDF automáticamente |
| `signed_at` | date | Fecha de firma (puede diferir de created_at) |
| `notes` | text | Notas internas |
| `created_by` | FK users | Usuario que creó el documento |
| `parent_document_id` | FK documents, nullable | Para agrupar versiones de un mismo documento (original → firmado) |

### 5.3 Campo `content` — estructura por tipo

El `content` es JSON flexible. Cada tipo de documento tiene su propio schema. El backend valida el schema según `document_type_id`. El frontend construye el formulario basado en el tipo seleccionado.

**`content` para Acta de Entrega/Devolución** (extraído del acta real analizada):
```json
{
  "handover_type": "restitution",
  "property_condition": "regular estado, sin resanar, sin pintar, con varios daños",
  "pending_services": true,
  "pending_services_notes": "EPM factura mes vencido - asumido por Juan Carlos Duque",
  "pending_payments": [
    { "concept": "canon febrero 2026", "amount": 1316000 },
    { "concept": "canon marzo 2026", "amount": 10230400 }
  ],
  "total_pending": 37377200,
  "cost_document_transaction": 500000,
  "photos_taken": true,
  "obligations_persist": true,
  "obligations_notes": "Al recibir el inmueble no se extinguen las obligaciones contractuales",
  "signatories": [
    { "name": "Maria Teresa Restrepo Q", "cc": "...", "role": "ocupante" }
  ]
}
```

**`content` para Contrato de Arrendamiento** (solo campos adicionales no cubiertos por `Rent`):
```json
{
  "clauses_additional": [
    "Los servicios públicos, el IVA y la administración están incluidos en el canon",
    "Los baños son comunes para todos los locales..."
  ],
  "annexes": ["inventario del bien inmueble"],
  "legal_framework": "decreto_410_1971"
}
```

> Para contratos, la mayoría de los datos vienen del `Rent` directamente. El `content` del Document-contrato es mínimo.

### 5.4 Manejo de múltiples archivos por documento

No se crea tabla `document_files` separada. En cambio:

- **Contrato original (PDF generado)** → `Document` con `template_key` y `status = generado`
- **Contrato firmado (PDF subido)** → otro `Document` con `parent_document_id` apuntando al original, `status = firmado`
- **Fotos del acta** → `Document` con `document_type = foto_acta`, `parent_document_id` apuntando al acta principal

Esto mantiene la tabla `documents` como fuente única de verdad para todos los archivos.

---

## 6. Relaciones del módulo

```
Rent ────────────────────────── Property
 │                                  │
 ├── tenants[] (Person)             ├── owners[] (Person via pivot)
 ├── codebtors[] (Person)           └── obligations[] (PropertyObligation)
 ├── liabilities[] (Liability)
 │
 └── documents[] (Document — polimórfico documentable)
         │
         ├── children[] (Document — via parent_document_id)
         ├── Lookup (document_category)
         ├── Lookup (document_type)
         └── Lookup (document_status)

Property
 └── documents[] (Document — polimórfico)
     (ej: pólizas, actas de inspección, documentos de la propiedad)

Person
 └── documents[] (Document — polimórfico)
     (ej: documentos de identidad, certificados)
```

---

## 7. Snapshot de Obligaciones en el Contrato (`rent_obligations`)

Al crear un `Rent`, las obligaciones vigentes de la propiedad se copian a `rent_obligations`. Si el predial sube al año siguiente o cambia la cuota del conjunto, el contrato histórico no se ve afectado. Esta tabla es inmutable desde que se firma el contrato.

### Tabla `rent_obligations` (nueva — crear migración)

```sql
rent_obligations
├── id
├── rent_id                 FK rents
├── obligation_type_id      FK lookups (predial, hipoteca, administracion, mantenimiento...)
├── amount                  decimal(12,2)   -- monto en el momento del contrato
├── total                   decimal(12,2)   -- obligación total (ej: saldo hipoteca)
├── frequency_type_id       FK lookups (monthly, yearly, one_time)
├── expiration_date         date nullable
├── paid_by                 string          -- 'owner' | 'tenant'
├── description             text nullable
├── created_at / updated_at / deleted_at
```

**Flujo al crear el Rent:**
1. Sistema carga `property.obligations[]` y las pre-muestra en el formulario
2. El usuario confirma cuáles aplican y quién las paga en este contrato (`paid_by`)
3. Se guardan en `rent_obligations` — copia fija desde ese momento
4. El acta de devolución lee `rent.obligations[]` para mostrar las cargas que existían durante el contrato

**Datos que se leen de relaciones (no nuevos inputs):**
- `IVA` → calculado del tipo de contrato + `property.owners[0].fiscalProfile.vat_withholding`; pre-llena el campo `iva` del Rent pero el usuario puede ajustarlo
- `actividad económica` → mostrada del `FiscalProfile` del arrendatario como referencia; el campo `activity` del Rent es input libre editable
- `obligaciones` → pre-cargadas desde `property.obligations[]`, confirmadas y snapshotteadas en `rent_obligations`

---

## 7b. Obligaciones de la Propiedad (`property_obligations`)

La tabla `property_obligations` **ya existe** en la migración de properties y está **bien ubicada**. No es parte del módulo Documents sino del módulo Properties, pero se menciona aquí porque tiene impacto en los contratos.

**Por qué pertenece a Property y no a Rent:**
- Representan cargas permanentes del inmueble: impuesto predial, hipoteca, cuota de administración del conjunto, mantenimiento de ascensores, etc.
- Existen independientemente de si hay un arriendo activo o no
- El propietario necesita verlas para calcular la rentabilidad real del inmueble
- Al crear un contrato, la inmobiliaria consulta las obligaciones para definir si la administración va incluida en el canon (`administration_included` en Rent) o la paga el arrendatario aparte

**Relación con `Liability` (en Rent):** `Liability` registra qué responsabilidades del inmueble asume el arrendatario dentro del contrato específico (ej: el arrendatario asume la cuota de administración). `PropertyObligation` registra las cargas del inmueble sin importar quién las paga. No son lo mismo — los dos tienen sentido.

---

## 8. Flujo de creación de un Contrato (proceso completo)

```
1. Registrar personas: Propietario + Arrendatario + Codeudores (Person)
2. Registrar propiedad (Property) → asignar propietario(s) con % participación
   └── Registrar obligaciones de la propiedad (PropertyObligation)
3. Crear Rent:
   - property_id ya existe en la migración — conectar Property
   - Seleccionar contract_type (define la plantilla PDF)
   - Llenar condiciones: canon, IVA, período, incremento, fechas, destinación...
   - Agregar arrendatario(s) y codeudor(es)
   - Definir si administration_included
4. Desde el Rent → crear Document tipo 'contrato':
   - Se auto-completa con datos del Rent, Property, Persons, Company
   - Agregar cláusulas adicionales si hay (content.clauses_additional)
   - Generar PDF (template_key define la plantilla → generated_at se llena)
5. Firmar: subir PDF firmado → nuevo Document con parent_document_id + status = firmado
6. Al iniciar el arriendo → crear Document tipo 'acta_entrega':
   - Estado del inmueble, servicios, deudas, firmas (en content JSON)
   - Fotos → Documents tipo 'foto_acta' con parent_document_id = acta
7. Durante el arriendo:
   - Documents tipo 'factura' → LeaseFee module (futuro)
   - Documents tipo 'poliza' → Warranty module
8. Al terminar → crear Document tipo 'acta_devolucion':
   - Estado final, deudas pendientes, firmas
```

---

## 9. Lo que NO cambia del backend actual

- La migración base de `rents` — se modifica, no se reemplaza
- El pivote `rent_tenant_codebtor` — sigue siendo la forma de asociar personas al arriendo
- El módulo `Warranty` y `LeaseFee` siguen siendo módulos separados
- El polimorfismo de `documents` ya usa `documentable_type / documentable_id` — igual que `Image`
- La tabla `property_obligations` — ya existe y está bien, solo necesita su modelo y CRUD

---

## 10. Permisos del módulo

```
documents.view        — ver documentos
documents.create      — crear y editar documentos en borrador
documents.generate    — generar PDF desde plantilla
documents.sign        — subir versión firmada
documents.archive     — archivar documentos
documents.delete      — eliminar (solo borradores)
documents.export      — descargar PDFs
```

---

## 11. Orden de implementación recomendado

> **Regla de verificación:** cada ítem de cada fase debe cumplir los tres puntos antes de considerarse terminado:
> 1. **Funciona**: el endpoint responde correctamente con datos reales (usar Tinker o el cliente HTTP del frontend)
> 2. **No rompe lo existente**: correr el listado y detalle de los módulos ya implementados (Property, Person, Company, User) y confirmar que siguen respondiendo sin error
> 3. **Auditoría activa**: si el ítem toca un modelo con `LogsActivity`, verificar que el log se genera en `activity_log` con el `batch_uuid` correcto

### Fase 1 — Completar `Rent` (pre-requisito para todo lo demás)

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 1 | Modificar `create_rents_table` → agregar campos faltantes (sección 4.2) | `GET /api/properties` y `GET /api/people` siguen funcionando |
| 2 | Seeders: Lookups nuevos (`contract_type`, `increment_type`, `document_*`) | `GET /api/lookups` retorna los nuevos registros |
| 3 | CRUD completo de `Rent`: modelo, service, repository, controller, validaciones, resource | `POST /api/rents` crea con todos los campos; `GET /api/rents` lista correctamente |
| 4 | Modelo `Liability` — completar y asociar al Rent | Rent store/update con `liabilities[]` sincroniza sin error |

### Fase 2 — Módulo Document (core)

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 5 | Modificar `create_documents_table` → agregar campos faltantes (sección 5.2) | `GET /api/rents` sigue funcionando |
| 6 | Modelo `Document` — polimórfico, `parent_document_id`, relaciones a Lookup/User | `php artisan tinker` → `Document::first()` sin error |
| 7 | CRUD básico de Documents asociados a un Rent | `GET /api/rents/{id}/documents` lista; `POST` crea documento en borrador |
| 8 | Permisos `documents.*` + seeder | Usuario sin permiso recibe 403; con permiso, 200 |

### Fase 3 — Generación de contratos (✅ Parcialmente completada)

| # | Tarea | Estado |
|---|---|---|
| 9 | Plantilla Blade arrendamiento vivienda (Ley 820/2003) | ✅ `documents.contracts.residential` |
| 10 | Plantilla Blade arrendamiento comercial (Decreto 410) | ✅ `documents.contracts.commercial` |
| 11 | Plantilla Blade administración/mandato | ✅ `documents.contracts.administracion-mandato` |
| 12 | Plantilla Blade comodato | ✅ `documents.contracts.comodato` |
| 13 | Plantilla Blade colocación | ✅ `documents.contracts.colocacion` |
| 14 | `POST /api/documents/{id}/generate` | ✅ Implementado |
| 15 | `GET /api/documents/{id}/download` | ✅ Implementado |

### Fase 4 — Actas (✅ Completada)

| # | Tarea | Estado |
|---|---|---|
| 16 | Plantilla Blade acta de entrega | ✅ `documents.actas.entrega` |
| 17 | Plantilla Blade acta de devolución | ✅ `documents.actas.devolucion` |
| 18 | Plantilla Blade acta de inspección | ✅ `documents.actas.inspeccion` |
| 19 | Formulario modal con campos de content por tipo | ✅ Frontend `rents/documents/index.vue` |

### Fase 4b — Otros tipos de documento (✅ Completada — 2026-07-12)

> **Todos los 14 tipos con plantilla Blade están implementados.** `foto_acta` no tiene PDF (son imágenes).

| # | Tipo | Template key | Estado |
|---|---|---|---|
| 20 | Factura canon | `canon` | ✅ `documents.facturas.canon` |
| 21 | Liquidación final | `liquidacion` | ✅ `documents.facturas.liquidacion` |
| 22 | Póliza seguro arrendamiento | `seguro_arrendamiento` | ✅ `documents.polizas.seguro-arrendamiento` |
| 23 | Garantía codeudor | `codeudor` | ✅ `documents.garantias.codeudor` |
| 24 | Inventario inmueble | `inventario_inmueble` | ✅ `documents.inventario.inventario-inmueble` |
| 25 | Preaviso terminación | `preaviso_terminacion` | ✅ `documents.preaviso.terminacion` |

**Campos de formulario implementados por tipo:**
- `acta / inspeccion`: estado inmueble, servicios, pagos pendientes, fotos, observaciones, inspector, próxima inspección
- `preaviso`: remitente (arrendador/arrendatario), fecha terminación, días preaviso, motivación
- `inventario`: estado general, fotografías, descripción/notas
- `poliza`: aseguradora, N° póliza, suma asegurada, prima, vigencia, beneficiario
- `garantia`: tipo de garantía, observaciones
- `factura_canon`: período, fecha límite de pago, mora
- `factura_liquidacion`: lista de ítems (cargos/abonos), observaciones

**Botón "Generar PDF"** deshabilitado automáticamente cuando `template_key` no tiene plantilla Blade (`foto_acta`).

### Fase 5 — Firma electrónica

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 18 | Tabla `document_signatories` + modelo | Migración corre sin error |
| 19 | Flujo de envío de correo con token | Correo llega con URL válida; token existe en DB |
| 20 | Ruta pública `GET /sign/{token}` | Sin token → 404; token expirado → 410; válido → 200 con PDF |
| 21 | `POST /sign/{token}` — guarda firma | `DocumentSignatory.status = signed`; imagen guardada en storage |
| 22 | PDF final con firmas embebidas | PDF descargable con las imágenes de firma en las posiciones correctas |

### Fase 6 — Otros documentos

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 23 | Preaviso de terminación | PDF generado con fechas y partes correctas |
| 24 | Inventario del inmueble | `content` JSON con lista de ítems del inventario |
| 25 | Pólizas y garantías | Integrar con `Warranty` module existente |

### Fase 7 — Plantillas de contrato editables por el usuario (✅ Completada — 2026-07-12)

**Objetivo:** el usuario (inmobiliaria) puede editar la redacción de las cláusulas, renombrar secciones, reordenarlas o eliminarlas desde el admin, sin tocar código.

**Arquitectura implementada:**

- Tabla `contract_clauses` (tenant): `id`, `template_key`, `section_key`, `heading`, `body` (text con `{{VARIABLE}}`), `sort_order`, `is_active`, `is_default`
- Auto-seed: cuando el tenant no tiene cláusulas, `DocumentPdfService::loadClauses()` las inserta automáticamente desde `ContractClauseDefaultsService` (hardcoded)
- Al generar PDF: `loadClauses()` → `replaceVariables()` (22 variables) → Blade `@foreach($clauses as $clause)` → DomPDF
- Los Blade templates `residential.blade.php` y `commercial.blade.php` ya no tienen cláusulas hardcodeadas — usan el `$clauses` inyectado

**Variables disponibles (22 definidas):**
`{{CANON_MENSUAL}}`, `{{IVA_PORCENTAJE}}`, `{{IVA_MONTO}}`, `{{TOTAL_MENSUAL}}`, `{{FECHA_INICIO}}`, `{{FECHA_FIN}}`, `{{DURACION_MESES}}`, `{{DESTINACION}}`, `{{ACTIVIDAD}}`, `{{DIRECCION_INMUEBLE}}`, `{{MUNICIPIO_INMUEBLE}}`, `{{TIPO_INCREMENTO}}`, `{{FECHA_REAJUSTE}}`, `{{CIUDAD_FIRMA}}`, `{{FECHA_FIRMA}}`, `{{NOMBRE_ARRENDATARIO}}`, `{{DOCUMENTO_ARRENDATARIO}}`, `{{NOMBRE_CODEUDOR}}`, `{{DOCUMENTO_CODEUDOR}}`, `{{NOMBRE_EMPRESA}}`, `{{NIT_EMPRESA}}`, `{{COMISION_PORCENTAJE}}`

**Archivos del backend:**
- `database/migrations/tenant/2026_07_12_100000_create_contract_clauses_table.php`
- `app/Models/ContractClause.php`
- `app/Repositories/IContractClauseRepository.php` + `Implements/ContractClauseRepository.php`
- `app/Services/IContractClauseService.php` + `Implements/ContractClauseService.php`
- `app/Services/Implements/ContractClauseDefaultsService.php` — cláusulas por defecto de los 5 tipos de contrato
- `app/Http/Controllers/ContractClauseController.php` — 6 endpoints (index, store, update, destroy, reorder, resetToDefaults, meta)
- `app/Http/Requests/StoreContractClauseRequest.php` + `UpdateContractClauseRequest.php`
- `routes/api.php` — grupo `contract-clauses.*` con permisos `documents.view` / `documents.create`

**Archivos del frontend:**
- `frontend/app/interfaces/IContractClause.ts`
- `frontend/app/services/ContractClauseService.ts`
- `frontend/app/pages/contract-templates/index.vue` — editor de plantillas con drag&drop
- `frontend/public/data/sidebar.json` — enlace "Plantillas" bajo "Contratos"

| # | Tarea | Estado |
|---|---|---|
| 26 | Tabla `contract_clauses` + defaults hardcodeados en `ContractClauseDefaultsService` | ✅ Migración aplicada; 5 tipos de contrato con cláusulas completas |
| 27 | CRUD API `/api/contract-clauses` + reorder + reset | ✅ 28 rutas registradas (multi-dominio) |
| 28 | `DocumentPdfService` carga cláusulas desde DB con auto-seed fallback | ✅ PDF vivienda y comercial verificados con cláusulas editables |
| 29 | UI admin — página Plantillas con drag&drop, edición inline, variables reference | ✅ `contract-templates/index.vue` compilando sin errores |

---

## 12. Decisiones de arquitectura importantes

### ¿Por qué `content` JSON en Document?
Cada tipo de documento tiene campos diferentes. Un JSON flexible evita tener múltiples tablas especializadas. El schema del JSON se valida en el backend con reglas específicas según `document_type_id`. Si un tipo crece demasiado en campos, se puede migrar a tabla propia sin romper la API.

### ¿Por qué el contrato de arrendamiento tiene sus condiciones en `Rent` y no en `Document.content`?
Las condiciones (canon, fechas, incremento, etc.) son datos operativos del arrendamiento que otros módulos necesitan (LeaseFee calcula cuánto cobrar, Warranty sabe hasta cuándo aplica). Meterlas en un JSON de Document las haría inaccesibles para queries. El Document-contrato es solo la *representación PDF* de esos datos.

### ¿No es redundante `document_files` separado?
Sí — la migración existente ya tiene campos de archivo en `documents`. Se usa `parent_document_id` para agrupar versiones del mismo documento en lugar de crear otra tabla.

### ¿El Contrato de Administración/Mandato va en un Rent también?
Decisión pendiente. Opciones: (a) un `Rent` de `contract_type = administracion_mandato` para la relación inmobiliaria-propietario, o (b) un `Document` standalone del `Property`. Revisar cuando se llegue a esa fase.

---

## 13. Campos del contrato en los PDFs analizados vs backend actual

| Campo en el PDF | Columna en DB | Tabla | Estado |
|---|---|---|---|
| Nº contrato (46952) | `contract_number` | `rents` | ❌ falta |
| Tipo (Local Comercial / Vivienda) | `contract_type_id` | `rents` → Lookup | ❌ falta |
| Datos inmobiliaria | `Company` | `companies` | ✅ existe |
| Datos arrendatario | `tenants[]` | `rent_tenant_codebtor` | ✅ existe |
| Datos codeudores | `codebtors[]` | `rent_tenant_codebtor` | ✅ existe |
| Datos inmueble | `property_id` | `rents` → `properties` | ✅ ya existe en migración |
| Matrícula, estrato, dirección | campos de `Property` | `properties` | ✅ existe |
| Canon | `canon` | `rents` | ❌ falta |
| IVA | `iva` | `rents` | ❌ falta |
| Administración incluida | `administration_included` | `rents` | ❌ falta |
| Sometido a P.H | `is_ph` | `rents` | ❌ falta |
| Día de pago | `period` (date) | `rents` | ✅ existe — evaluar si suficiente |
| Incremento % / IPC / IPC+puntos | `interest_rate` (string) + `increment_type_id` | `rents` | ⚠️ parcial |
| Fecha reajuste | `adjustment_date` | `rents` | ❌ falta |
| Fecha inicio / terminación | `start_date` / `end_date` | `rents` | ✅ existe |
| Duración | `duration` | `rents` | ✅ existe |
| Destinación | `destination` (string) | `rents` | ✅ existe — evaluar si migrar a FK Lookup |
| Actividad | `activity` | `rents` | ✅ existe |
| Asegurado | `is_insured` | `rents` | ❌ falta |
| Cuenta bancaria pago | `consignment_account` | `rents` | ✅ existe |
| Banco | `payment_bank_id` | `rents` | ❌ falta |
| Ciudad firma | `signed_city` | `rents` | ❌ falta |
| Fecha firma | `signed_at` | `documents` | ❌ falta (agregar a migración existente) |
| Cláusulas adicionales | `additional_clauses` (JSON) | `rents` | ❌ falta |
| Contrato firmado (PDF) | `Document` con parent | `documents` | ❌ modelo sin implementar |
| Estado del inmueble (acta) | `content.property_condition` | `documents` | ❌ campo `content` falta |
| Fotos del acta | `Document` hijo con `parent_document_id` | `documents` | ❌ campo falta |
| Deudas pendientes del acta | `content.pending_payments` | `documents` | ❌ campo `content` falta |
| Firmas del acta | `content.signatories` | `documents` | ❌ campo `content` falta |

---

## 14. Firma Electrónica — Flujo por correo y URL

### Fundamento legal (Colombia)
La Ley 527/1999 (Ley de Comercio Electrónico) valida la firma electrónica simple para contratos de arrendamiento. No se requiere firma digital con certificado. Es suficiente con: identificar al firmante (email conocido + token único), registrar su intención de firmar, y preservar la integridad del documento.

### Tabla `document_signatories` (nueva — crear migración)

```sql
document_signatories
├── id
├── document_id             FK documents
├── person_id               FK people nullable    -- si es persona registrada en el sistema
├── name                    string               -- snapshot del nombre al momento de envío
├── email                   string               -- correo donde se envía el link
├── role                    string               -- 'arrendatario' | 'arrendador' | 'codeudor' | 'inmobiliaria'
├── order                   tinyint              -- orden de firma (1 firma primero, luego 2, etc.)
├── token                   string unique        -- UUID para la URL pública de firma
├── token_expires_at        timestamp            -- expiración (ej: 30 días)
├── status                  string               -- pending | viewed | signed | rejected | expired
├── viewed_at               timestamp nullable   -- cuándo abrió el link
├── signed_at               timestamp nullable   -- cuándo firmó
├── signature_type          string nullable      -- 'drawn' | 'uploaded'
├── signature_path          string nullable      -- path en storage a la imagen de firma
├── ip_address              string nullable      -- IP al momento de firmar
├── user_agent              string nullable      -- navegador/dispositivo
├── rejection_reason        text nullable
├── created_at / updated_at
```

### Flujo completo

```
1. Documento generado (PDF listo, status = generado)

2. Inmobiliaria define los firmantes desde el admin:
   - Se pre-cargan desde el Rent (arrendatario, codeudores, etc.)
   - Se asigna orden si el flujo es secuencial

3. Sistema crea un DocumentSignatory por firmante:
   - token = UUID único
   - token_expires_at = now() + 30 días
   - status = pending

4. TenantMailer envía correo a cada firmante con:
   - Resumen del documento
   - Botón → https://{tenant}.inmobiliaria.com/firmar/{token}

5. Firmante abre la URL (ruta pública, sin login requerido):
   - Backend valida token (existe, no expirado, no ya firmado)
   - Actualiza viewed_at y status = viewed
   - Frontend muestra:
     a. PDF completo embebido (scrollable, no descargable)
     b. Área de firma: canvas para dibujar O botón para subir imagen
     c. Checkbox: "He leído el documento y acepto esta firma como válida"
     d. Botón "Firmar"

6. Al enviar la firma:
   - Guarda signature_path (imagen en storage)
   - Registra signed_at, ip_address, user_agent
   - status = signed
   - Si TODOS los signatarios han firmado:
     → Genera PDF final con firmas embebidas en las posiciones correctas
     → Document.status = firmado, Document.signed_at = now()
     → Envía correo de confirmación a todos con el PDF firmado adjunto

7. Si el token expira → status = expired, TenantMailer envía recordatorio
```

### Endpoints necesarios (rutas públicas — sin sanctum)

```
GET  /sign/{token}           -- muestra la interfaz de firma (SPA o Blade)
POST /sign/{token}           -- recibe la firma (multipart: imagen o canvas blob)
GET  /sign/{token}/document  -- sirve el PDF para preview (solo si token válido)
```

### Posiciones de firma en el PDF

- **Plantillas Blade (sistema)**: posiciones hardcodeadas en la vista, cada firmante tiene su bloque predefinido en el PDF
- **Plantillas custom del cliente**: el cliente marca la posición de cada firma con una variable especial: `${FIRMA_ARRENDATARIO}`, `${FIRMA_CODEUDOR_1}`, `${FIRMA_INMOBILIARIA}` — el sistema sustituye esa variable por la imagen de firma al generar el PDF final

### Consideraciones importantes

- La URL de firma es del **tenant** (la inmobiliaria), no de un dominio central — cada cliente usa su propio dominio/subdominio
- El PDF solo se muestra en preview; no se puede descargar hasta que esté completamente firmado
- El `token` es el único factor de autenticación — no reemplaza al login para operaciones admin
- Guardar `ip_address` + `user_agent` + `signed_at` es el audit trail legal mínimo
- Si se requiere firma secuencial: el correo del firmante 2 solo se envía cuando el firmante 1 firma

---

## 15. Plantillas Personalizadas por Cliente

La generación de PDFs tiene dos modos: plantillas del sistema y plantillas del cliente.

### Modo 1 — Plantillas del sistema (Blade + DomPDF)

Las plantillas están en el código (`resources/views/documents/`), una por `template_key`. Son las plantillas estándar legalmente correctas que vienen con el SaaS.

### Modo 2 — Plantillas del cliente (variable substitution)

El cliente sube su propia plantilla (DOCX o HTML) con variables del sistema embebidas.

**Variables disponibles (catálogo provisto en la UI):**

| Variable | Valor |
|---|---|
| `${NOMBRE_ARRENDATARIO}` | Nombre completo del arrendatario |
| `${CC_ARRENDATARIO}` | Cédula del arrendatario |
| `${NOMBRE_CODEUDOR_1}` | Nombre del primer codeudor |
| `${CANON}` | Valor del canon en números |
| `${CANON_LETRAS}` | Canon en letras (cuatrocientos mil pesos) |
| `${CANON_MAS_IVA}` | Canon + IVA |
| `${FECHA_INICIO}` | Fecha de inicio del contrato |
| `${FECHA_FIN}` | Fecha de terminación |
| `${DURACION_MESES}` | Duración en meses |
| `${DIRECCION_INMUEBLE}` | Dirección del inmueble |
| `${MUNICIPIO}` | Municipio del inmueble |
| `${CODIGO_INMUEBLE}` | Código de la propiedad |
| `${NOMBRE_INMOBILIARIA}` | Nombre de la inmobiliaria |
| `${NIT_INMOBILIARIA}` | NIT de la inmobiliaria |
| `${FIRMA_ARRENDATARIO}` | Imagen de la firma (solo para PDF final) |
| `${FIRMA_CODEUDOR_1}` | Imagen de firma del codeudor 1 |
| `${FIRMA_INMOBILIARIA}` | Imagen de firma de la inmobiliaria |

**Flujo de plantilla custom:**
1. Cliente sube su DOCX/HTML desde el panel admin
2. Sistema parsea el archivo, detecta las variables usadas, lista las no reconocidas
3. Cliente hace preview con un contrato de ejemplo
4. Si aprueba, se guarda como plantilla activa para ese `document_type`
5. En generación: el sistema sustituye variables + convierte a PDF

**Implementación**: no construir el motor de plantillas custom en la Fase 3. Agregarlo en una fase posterior (Fase 6+). Las primeras versiones usan solo Blade. La arquitectura del `DocumentService` debe abstraer el "motor de renderizado" para que cambiar de Blade a substitución custom sea transparente para el resto del código.

---

## 15b. Seeder — Datos iniciales de prueba

Al implementar el módulo se deben crear seeders con datos de ejemplo para desarrollo y QA.

### `DocumentLookupSeeder`
Inserta todos los Lookups nuevos del módulo (no existe aún ninguno de estos en la DB):

| Categoría | Valores a insertar |
|---|---|
| `contract_type` | arrendamiento_vivienda, arrendamiento_comercial, administracion_mandato, comodato, colocacion |
| `increment_type` | porcentaje_fijo, ipc, ipc_mas_puntos |
| `document_category` | contrato, acta, factura, poliza, garantia, inventario, preaviso, otro |
| `document_type` | arrendamiento_vivienda, arrendamiento_comercial, administracion_mandato, comodato, colocacion, entrega_inmueble, devolucion_inmueble, inspeccion, canon, liquidacion, seguro_arrendamiento, codeudor, inventario_inmueble, preaviso_terminacion, foto_acta |
| `document_status` | borrador, generado, enviado, firmado, archivado, anulado |

### `DocumentPermissionSeeder`
Crea los permisos del módulo:
```
documents.view, documents.create, documents.generate,
documents.sign, documents.archive, documents.delete, documents.export
```
Asignarlos al rol superadmin del tenant automáticamente.

### `RentContractSeeder` (datos de prueba — solo dev/staging)

Crear los siguientes contratos de prueba para poder verificar la generación de PDFs y el flujo de firma:

**Contrato 1 — Arrendamiento Vivienda (Ley 820)**
```
Nº: 47476
Tipo: arrendamiento_vivienda
Propiedad: P117-01 — Carrera 42 #88-128, Piso 3, Manrique Las Granjas, estrato 2
Arrendataria: Laura Juliana Nempeque Murcia (CC: XXXXXXXX)
Codeudor: Yenny Fabiola Perez Puerto
Canon: $750.000
Administración: incluida
Asegurado: sí | Sometido a P.H.: sí
Incremento: IPC
Inicio: 2026-07-06 | Fin: 2027-07-05 | Duración: 12 meses
Destino: vivienda
```

**Contrato 2 — Arrendamiento Comercial (Decreto 410)**
```
Nº: 47429
Tipo: arrendamiento_comercial
Propiedad: P363-05 — Guarne, Km 21
Arrendatario: NUTRIPRIME S.A.S
Codeudores: 2 personas naturales
Canon: $12.000.000 + IVA $2.280.000 = $14.280.000
Administración: incluida | Sometido a P.H.: sí
Incremento: IPC + 2 puntos
Inicio: 2026-09-01 | Fin: 2031-08-31 | Duración: 60 meses
Destino: comercial | Actividad: Fabricación de Alimentos Balanceados
```

**Acta de Entrega de prueba**
```
Vinculada al Contrato 1 (vivienda)
Estado inmueble: "buenas condiciones, recién pintado"
Servicios pendientes: no
Deuda pendiente: $0
Fotos: 3 imágenes de prueba
Firmantes: arrendataria + inmobiliaria
```

**Firmantes de prueba para flujo e-signature:**
- Arrendataria Contrato 1 → token válido por 30 días
- Arrendatario empresa Contrato 2 → token válido por 30 días

**Verificación esperada del seeder:**
- Los dos contratos aparecen en el listado de Documents/Rents
- Se puede generar el PDF de cada uno desde el admin
- Los correos de firma se envían a emails de prueba configurados en `.env`
- Al abrir la URL `/sign/{token}` se muestra el documento y el canvas de firma

---

## 16. Referencia cruzada con dominio.md

| En dominio.md | Cómo se implementa aquí |
|---|---|
| `Document 📋` | Tabla `documents` ya existe — completar modelo y agregar campos |
| `Warranty 📋` | Document tipo póliza + tabla `warranties` (módulo propio pendiente) |
| `LimitDate 📋` | Ya hay `limit_dates_id` en `rents`, más `start_date`, `end_date`, `adjustment_date` |
| `Liability 📋` | Completar modelo y CRUD — diferente a `property_obligations` |

Los módulos `LeaseFee` y `Warranty` siguen siendo módulos independientes — el módulo Document los complementa pero no los reemplaza.
