# Módulo de Sucursales — Plan de implementación

> **Estado**: Planificación — no hay código escrito aún  
> **Fecha**: 2026-07-10

---

## 1. Visión del módulo

Una **sucursal** es una unidad operativa de la inmobiliaria con su propia dirección, contactos, staff y cartera de propiedades/contratos. La empresa principal (sede central) es también una sucursal desde el punto de vista del modelo — la que tiene `parent_company_id = null`.

El sistema debe permitir:
- Crear y gestionar sucursales desde el panel admin
- Asignar usuarios a una o más sucursales
- Cambiar de sucursal activa mediante un selector en el nav (con permiso)
- Que los datos (propiedades, personas, contratos) queden asociados a una sucursal via `company_id`
- Que el módulo de auditoría registre en qué sucursal ocurrió cada acción

---

## 2. Decisión de modelo — `companies` auto-referencial

**Decisión: auto-referencial en `companies`, usando `company_id` como identificador de sucursal en todas las tablas.**

Una sucursal tiene exactamente los mismos atributos que una empresa: nombre, NIT propio o no, dirección, contactos, logo, staff. Ya existe la infraestructura completa en `companies` + `addresses` + `contacts` + `images`. Además `contacts`, `addresses` y `publish_channels` ya usan `company_id` como FK — usar el mismo nombre es coherente con todo el proyecto.

**Por qué `company_id` y no `branch_id`:** las sucursales SON companies. El nombre `company_id` es el que ya usa el resto del sistema para relacionar entidades con la empresa. Llamarlo `branch_id` introduciría un nombre distinto para el mismo concepto.

**Campos a agregar en la migración existente `create_companies_table`:**

```sql
-- Agregar a 2025_09_02_214009_create_companies_table.php
$table->uuid('parent_company_id')->nullable()->comment('null = sede principal, FK = sucursal');
$table->string('branch_code')->nullable()->comment('Código interno de la sucursal ej: SUC-001');
$table->boolean('is_active')->default(true)->comment('Sucursal activa o inactiva');

$table->foreign('parent_company_id')->references('id')->on('companies');
```

---

## 3. Qué tablas reciben `company_id` y cuáles no

### Tablas que SÍ necesitan `company_id` (la sucursal que gestiona el registro)

| Tabla | Motivo | Migración a modificar |
|---|---|---|
| `properties` | Cada propiedad la gestiona una sucursal | `create_properties_table` |
| `people` | La sucursal que registró/gestiona esa persona | `create_people_table` |
| `rents` | Cada contrato lo administra una sucursal | `create_rents_table` |

```sql
-- En cada una de las tres tablas:
$table->uuid('company_id')->nullable()->comment('Sucursal que gestiona este registro');
$table->foreign('company_id')->references('id')->on('companies');
$table->index('company_id');
```

### Tablas que NO necesitan `company_id` adicional (y por qué)

| Tabla | Razón |
|---|---|
| `companies` | Es la tabla de sucursales en sí — `parent_company_id` define la jerarquía |
| `contacts` | Ya tiene `company_id` propio; hereda sucursal de su padre (`person_id`, `property_id`) |
| `addresses` | Ya tiene `company_id` propio; hereda sucursal de su padre |
| `publish_channels` | Ya tiene `company_id`; sigue a `property` |
| `account_banks` | Pertenece a `person` que ya tendrá `company_id` |
| `images` | Sigue a `property` / `company` que ya tienen `company_id` |
| `documents` | Sigue a `rent` / `property` que ya tienen `company_id` |
| `fiscal_profiles` | Configuración tributaria compartida — no es por sucursal |
| `lookups` | Catálogos globales del tenant |
| `warranties`, `liabilities`, `lease_fees` | Siguen a `rent → company_id` |
| `activity_log` | Ver sección 7 — sucursal en properties JSON de Spatie |
| `company_settings` | Ya está ligado a `company_id`; cada sucursal puede tener su setting |
| `realstate_site_settings` | Del tenant completo, no por sucursal |
| `economic_activities`, `taxe_types` | Siguen a `fiscal_profile` |
| `rent_tenant_codebtor` | Sigue a `rent → company_id` |

---

## 4. Relación usuarios ↔ sucursales

Un usuario puede pertenecer a **una o varias sucursales**. Necesita tabla pivot nueva:

### Tabla `company_user` (nueva migración)

```sql
company_user
├── id
├── company_id   FK companies  -- la sucursal asignada
├── user_id      FK users
├── is_default   boolean       -- sucursal por defecto al hacer login
├── created_at / updated_at
-- Unique: company_id + user_id
```

**Regla:** El superadmin del tenant tiene acceso a todas las sucursales sin necesitar filas en `company_user`. Solo los usuarios con acceso restringido requieren filas explícitas.

---

## 5. Selector de sucursal en el nav — flujo

```
1. Usuario hace login
   └── Backend determina las sucursales accesibles:
       a. ¿Tiene permiso `companies.view_all`? → todas las companies del tenant donde parent_company_id IS NOT NULL + sede
       b. ¿Tiene permiso `companies.switch`? → sus registros de `company_user`
       c. Sin permiso especial → solo la sucursal por defecto, sin selector visible

2. Respuesta del login incluye:
   {
     "current_company_id": "uuid-sucursal-a",
     "accessible_branches": [
       { "id": "uuid-sede",       "name": "Sede Central",      "branch_code": "SEDE"    },
       { "id": "uuid-sucursal-a", "name": "Sucursal Laureles", "branch_code": "SUC-001" }
     ]
   }

3. Frontend (Pinia store) guarda current_company_id
   └── Si accessible_branches.length > 1 Y tiene permiso companies.switch → muestra selector en nav

4. Al cambiar de sucursal:
   POST /api/branch/switch  { company_id: "uuid-nueva-sucursal" }
   └── Backend valida que el usuario tiene acceso a esa company
   └── Actualiza current_company_id en sesión/claim del token
   └── Frontend refresca los datos (vuelve al dashboard de esa sucursal)

5. Cada request API lleva company_id resuelto automáticamente:
   X-Company-Id: uuid-sucursal-activa
   └── Middleware `ResolveBranchMiddleware` lo lee y lo inyecta en el contexto de la request
```

---

## 6. Módulo de gestión de sucursales

### CRUD de sucursales

| Endpoint | Acción | Permiso requerido |
|---|---|---|
| `GET /api/branches` | Listar sucursales del tenant | `companies.view` |
| `POST /api/branches` | Crear sucursal | `companies.create` |
| `GET /api/branches/{id}` | Ver detalle | `companies.view` |
| `PUT /api/branches/{id}` | Editar sucursal | `companies.edit` |
| `DELETE /api/branches/{id}` | Desactivar sucursal | `companies.delete` |
| `POST /api/branch/switch` | Cambiar sucursal activa en sesión | `companies.switch` |
| `GET /api/branches/{id}/users` | Usuarios de la sucursal | `companies.view` |
| `POST /api/branches/{id}/users` | Asignar usuario a sucursal | `companies.edit` |
| `DELETE /api/branches/{id}/users/{userId}` | Quitar usuario de sucursal | `companies.edit` |

### Jerarquía de Company como sucursal

```
Company (sede principal — parent_company_id = null)
   │
   ├── Company [SUC-001] (Sucursal Laureles — parent_company_id = sede.id)
   │       ├── addresses[]    (dirección propia via company_id)
   │       ├── contacts[]     (contactos propios via company_id)
   │       ├── logo (Image)   (logo propio o hereda del padre)
   │       ├── setting        (CompanySetting propio o hereda de sede)
   │       └── users[]        (via company_user pivot)
   │
   └── Company [SUC-002] (Sucursal Envigado — parent_company_id = sede.id)
           └── ...
```

Una sucursal hereda la configuración (`company_settings`) de la sede si no tiene la propia. Al crear una sucursal, se copia el setting de la sede como punto de partida.

---

## 7. Impacto en el módulo de Auditoría

La tabla `activity_log` no recibe columna adicional — es tabla de Spatie que no se modifica. El `company_id` de la sucursal se registra en el campo `properties` JSON que Spatie ya provee.

**En el middleware `ResolveBranchMiddleware`:**

```php
Activity::tap(function (Activity $activity) use ($companyId) {
    $activity->properties = $activity->properties->put('company_id', $companyId);
});
```

Así cada log queda con `properties.company_id` sin tocar el schema de Spatie.

**Filtrar auditoría por sucursal:**
```php
->where('properties->company_id', $companyId)
```

---

## 8. Permisos del módulo

```
companies.view         — ver lista de sucursales y detalle
companies.create       — crear nuevas sucursales
companies.edit         — editar datos de sucursal, asignar/quitar usuarios
companies.delete       — desactivar sucursal (soft)
companies.switch       — usar el selector de sucursal en el nav
companies.view_all     — ver datos cruzados de todas las sucursales (reportes globales)
```

**Relación con el rol superadmin del tenant:** el superadmin del tenant tiene implícitamente `companies.view_all` + `companies.switch` + todas las demás. No necesita filas en `company_user`.

---

## 9. Migración de datos existentes

Cuando se active el módulo, todos los registros sin `company_id` apuntan automáticamente a la **sede principal**:

```sql
-- Ejecutar como parte del seeder de activación del módulo
UPDATE properties SET company_id = (SELECT id FROM companies WHERE parent_company_id IS NULL LIMIT 1) WHERE company_id IS NULL;
UPDATE people    SET company_id = (SELECT id FROM companies WHERE parent_company_id IS NULL LIMIT 1) WHERE company_id IS NULL;
UPDATE rents     SET company_id = (SELECT id FROM companies WHERE parent_company_id IS NULL LIMIT 1) WHERE company_id IS NULL;
```

Garantiza que el sistema funciona igual para clientes de una sola oficina.

---

## 10. Impacto módulo por módulo

| Módulo | Impacto | Acción requerida |
|---|---|---|
| **Company** | Ahora soporta jerarquía | Agregar `parent_company_id`, `branch_code`, `is_active` a migración existente |
| **Property** | Scoped por sucursal | Agregar `company_id` a migración existente + scope en queries |
| **Person** | Scoped por sucursal | Agregar `company_id` a migración existente + scope en queries |
| **Rent** | Scoped por sucursal | Agregar `company_id` a migración existente + scope en queries |
| **User** | Asignado a sucursal(es) | Nueva migración `create_company_user_table` |
| **Document** | Hereda sucursal de Rent/Property | Sin cambio en migración |
| **Address** | Ya tiene `company_id` propio | Sin cambio |
| **Contact** | Ya tiene `company_id` propio | Sin cambio |
| **Image** | Hereda de Property/Company | Sin cambio |
| **FiscalProfile** | Compartido, no es por sucursal | Sin cambio |
| **Audit** | Sucursal en properties JSON | Agregar tap en middleware, filtro en endpoint |
| **Lookup** | Global, no es por sucursal | Sin cambio |
| **RealstateSite** | Del tenant completo | Sin cambio |
| **CompanySetting** | Cada sucursal puede tener el suyo via `company_id` | Sin cambio de schema |
| **LeaseFee / Warranty / Liability** | Heredan de Rent | Sin cambio |
| **Permissions** | Agregar permisos del módulo | Seeder de permisos |

---

## 11. Orden de implementación recomendado

> **Regla de verificación:** cada ítem debe cumplir los tres puntos antes de considerarse terminado:
> 1. **Funciona**: el endpoint o feature responde correctamente con datos reales
> 2. **No rompe lo existente**: los módulos ya implementados (Property, Person, Company, User, Audit) siguen respondiendo sin error después del cambio
> 3. **Auditoría activa**: si el ítem toca un modelo con `LogsActivity`, verificar que el log se genera correctamente en `activity_log`

### Fase 1 — Schema y modelo base

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 1 | Modificar `create_companies_table` → agregar `parent_company_id`, `branch_code`, `is_active` | `GET /api/company` sigue retornando la empresa sin error |
| 2 | Modificar `create_properties_table`, `create_people_table`, `create_rents_table` → agregar `company_id` nullable | `GET /api/properties`, `GET /api/people` siguen funcionando; datos existentes tienen `company_id = null` |
| 3 | Nueva migración `create_company_user_table` | Migración corre sin conflictos |
| 4 | Modelo `Company` → relaciones `parent()`, `branches()`, `users()` via pivot | `Company::first()->branches` retorna colección vacía sin error |
| 5 | Seeder permisos `companies.*` | Usuario superadmin tiene los permisos; usuario sin rol → 403 |
| 6 | Seeder datos de prueba (sección 14) | 3 companies creadas, `company_user` con los 4 usuarios de prueba |

### Fase 2 — CRUD de sucursales

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 7 | `ResolveBranchMiddleware` → lee `X-Company-Id`, valida acceso, inyecta en contexto | Request sin header funciona (usa sede por defecto); header inválido → 403 |
| 8 | `GET /api/branches` y `GET /api/branches/{id}` | Lista solo sucursales del tenant actual, no las de otros tenants |
| 9 | `POST /api/branches`, `PUT /api/branches/{id}` | Crea/actualiza con `addresses[]` y `contacts[]` via `syncHasMany`; auditoría genera log |
| 10 | `DELETE /api/branches/{id}` (desactivar) | `is_active = false`; la sede principal no se puede desactivar |
| 11 | `POST /api/branch/switch` | `current_company_id` actualizado; respuesta incluye `accessible_branches` |
| 12 | Tap Spatie → `company_id` en logs | `activity_log.properties` contiene `company_id` en cada acción |

### Fase 3 — Scoping de datos

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 13 | Local Scope `scopeForCompany($companyId)` en Property, Person, Rent | `Property::forCompany($id)->get()` filtra correctamente |
| 14 | `index()` de Property → aplica scope de sucursal activa | Usuario de SUC-001 solo ve propiedades de SUC-001 |
| 15 | `index()` de Person → aplica scope | Usuario de SUC-001 solo ve personas de SUC-001 |
| 16 | `index()` de Rent → aplica scope | Idem para contratos |
| 17 | Usuario con `companies.view_all` → omite scope | Superadmin ve propiedades de todas las sucursales |
| 18 | Filtro auditoría `?company_id=uuid` | Devuelve solo logs de esa sucursal |

### Fase 4 — Frontend (admin panel)

| # | Tarea | Verificar que no rompe |
|---|---|---|
| 19 | Store `branchStore` en Pinia → `currentBranch`, `accessibleBranches` | Al login, store se hidrata correctamente |
| 20 | Header `X-Company-Id` en todas las requests HTTP | Network tab muestra el header en cada llamada a la API |
| 21 | Selector de sucursal en el nav | Solo aparece si `accessible_branches.length > 1` y tiene `companies.switch` |
| 22 | Al cambiar sucursal → listas se refrescan | Sin recargar página, los datos cambian al contexto de la nueva sucursal |
| 23 | Módulo CRUD de sucursales en el admin | CRUD completo con direcciones y contactos |

---

## 12. Consideraciones de diseño importantes

### `company_id` es nullable al inicio
Mientras el cliente no active sucursales, todo funciona con `company_id = sede.id`. Sin ruptura para clientes de una sola oficina.

### No usar Global Scope de Eloquent
Los Global Scopes se aplican a todas las queries del modelo incluyendo relaciones internas. Usar Local Scope `scopeForCompany($companyId)` llamado explícitamente desde los repositorios.

```php
// En el Repositorio — explícito y controlado:
$this->model->forCompany($currentCompanyId)->jsonPaginate();
```

### Usuarios con `companies.view_all` omiten el scope
Para reportes globales, el repositorio detecta el permiso y omite el `forCompany()` scope.

### La sede principal sigue siendo una Company normal
La distinción es solo `parent_company_id IS NULL`. Todo el código existente sigue funcionando sin cambios.

---

## 13. Sitio Público — Temas, Plantillas y Colores

### Diagnóstico de la tabla actual

`realstate_site_settings` tiene: `template_set`, `theme` (JSON de colores), `pages` (JSON de contenido de todas las páginas) y sus `backup_*`. No tiene `company_id` — es **un único registro por tenant**, no por sucursal.

### Decisión: el sitio público lo controla únicamente la sede principal

**Por qué es correcto así:**

1. **Una marca, un sitio.** Una inmobiliaria colombiana tiene un solo sitio web que representa a toda la empresa. Las sucursales son unidades operativas internas — el cliente final no sabe ni le importa desde qué sucursal se gestiona una propiedad.

2. **No hay subdominios por sucursal.** El SaaS ya tiene un subdominio por tenant (`veltra.inmobiliaria.co`). Agregar subdominios por sucursal implicaría gestión de certificados TLS, DNS y configuración de Traefik por sucursal — complejidad innecesaria en este estadio.

3. **La arquitectura actual no tiene `company_id` en `realstate_site_settings`** — no hay ningún cambio de schema pendiente.

4. **El contenido del sitio (páginas, colores, plantilla) es de marca**, no operativo. Igual que el NIT o el logo principal — pertenece a la sede.

### Regla de permisos

El permiso `site-settings.theme-view` (y los relacionados al sitio público) **solo debe estar disponible para usuarios de la sede principal o con `companies.view_all`**. Un usuario exclusivo de Sucursal Laureles no debería ver ni editar el sitio público.

Implementar esto en el middleware o en el policy del módulo RealstateSite:

```php
// En RealstateSitePolicy o middleware del módulo:
// Solo puede gestionar el sitio si pertenece a la sede principal
// o tiene el permiso companies.view_all
if ($user->cannot('companies.view_all')) {
    $userCompany = $user->defaultBranch(); // via company_user
    abort_if($userCompany->parent_company_id !== null, 403);
}
```

### Lo que cada sucursal SÍ puede tener del sitio público

Aunque el sitio sea uno solo, hay contenido que puede personalizarse por sucursal dentro de ese sitio:

| Elemento | Dónde vive | Quién lo gestiona |
|---|---|---|
| Colores y plantilla visual | `realstate_site_settings.theme` | Solo sede |
| Contenido de páginas (hero, servicios, about) | `realstate_site_settings.pages` | Solo sede |
| Favicon, logos del sitio | `realstate_site_settings.pages.layout` | Solo sede |
| Nombre, NIT, logo de la empresa | `Company` (sede) | Solo sede |
| Dirección y teléfono de cada sucursal | `Company` + `addresses` + `contacts` | Cada sucursal la suya |
| Propiedades publicadas | `properties` con `company_id` | Cada sucursal gestiona las suyas |

### Cómo aparecen las sucursales en el sitio público

La página de contacto del sitio público puede mostrar automáticamente todas las sucursales activas del tenant. El frontend público consulta las sucursales vía el endpoint público de la empresa y las renderiza como "Nuestras oficinas":

```
Sede Central          Sucursal Laureles       Sucursal Envigado
Calle 50 #45-30       Carrera 76 #39-50       Calle 30 Sur #43C-120
Medellín              Medellín                Envigado
604 321 0000          304 100 0001            304 100 0002
```

Esto no requiere cambio de schema — se lee de `companies` filtrando por `parent_company_id IS NOT NULL` (o incluyendo la sede).

### Posible futuro: micro-sitio por sucursal

Si en el futuro un cliente pide que cada sucursal tenga su propio sitio diferenciado (colores distintos, plantilla distinta), la evolución del schema sería:

```sql
-- Futura adición (NO implementar ahora):
ALTER TABLE realstate_site_settings ADD COLUMN company_id UUID NULL;
-- Una fila sin company_id = sede (configuración base/fallback)
-- Una fila con company_id = sucursal = override de esa sucursal
```

La arquitectura actual lo permite sin ruptura. Pero **no implementar hasta que un cliente lo pida**.

---

## 14. Seeder — Datos iniciales de prueba

Al implementar el módulo se deben crear seeders con datos de ejemplo para desarrollo y QA. Separar en dos seeders:

### `BranchPermissionSeeder`
Crea los permisos del módulo usando el guard del tenant:
```
companies.view, companies.create, companies.edit, companies.delete, companies.switch, companies.view_all
```
Asignarlos al rol superadmin del tenant automáticamente.

### `BranchSeeder` (datos de prueba — solo entornos dev/staging)

Crear la siguiente estructura de prueba:

```
Inmobiliaria Veltra S.A.S (sede principal — parent_company_id = null)
   branch_code: SEDE
   Dirección: Calle 50 #45-30, Medellín, Antioquia
   Contacto: sede@veltra.com / 604 321 0000

├── Sucursal Laureles (parent_company_id = sede.id)
│   branch_code: SUC-001
│   Dirección: Carrera 76 #39-50, Medellín, Antioquia
│   Contacto: laureles@veltra.com / 304 100 0001

└── Sucursal Envigado (parent_company_id = sede.id)
    branch_code: SUC-002
    Dirección: Calle 30 Sur #43C-120, Envigado, Antioquia
    Contacto: envigado@veltra.com / 304 100 0002
```

**Usuarios de prueba en `company_user`:**

| Usuario | Sucursal | is_default | Descripción |
|---|---|---|---|
| admin@veltra.com | (todas — via `companies.view_all`) | — | Superadmin sin filas en pivot |
| laureles@veltra.com | SUC-001 | true | Solo accede a Laureles |
| envigado@veltra.com | SUC-002 | true | Solo accede a Envigado |
| multi@veltra.com | SUC-001 + SUC-002 | SUC-001 | Accede a dos sucursales, ve el selector |

**Propiedades de prueba distribuidas:**
- 3 propiedades con `company_id = SUC-001`
- 2 propiedades con `company_id = SUC-002`
- 1 propiedad con `company_id = SEDE`

**Verificación esperada:**
- El usuario `laureles@veltra.com` solo ve las 3 propiedades de su sucursal
- El usuario `multi@veltra.com` ve el selector en el nav; al cambiar de sucursal ve las propiedades correspondientes
- El usuario `admin@veltra.com` no ve el selector (tiene `view_all`, accede a todo sin necesidad de cambiar)
