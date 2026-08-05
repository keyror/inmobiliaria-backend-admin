# BACKEND — Guía para Claude

## Contexto
- **Stack**: Laravel 11 + Spatie Query Builder + Spatie Permission + Spatie Activitylog + Boost
- **Skills**: `laravel-api-architecture` (principal), `laravel-best-practices`, `laravel-permission-development`
- **Ubicación**: `backend/`

---

## Cuándo leer qué

| Tarea | Leer primero |
|---|---|
| Encontrar archivos de un módulo (controlador, servicio, repo) | Catálogo en `CLAUDE.md` raíz |
| Entender el negocio, módulos, relaciones entre entidades | [dominio.md](./dominio.md) |
| Crear feature completo (CRUD) | Activar skill `laravel-api-architecture` |
| Agregar validación | Activar skill `laravel-api-architecture` + [ejemplos/crear-rule.md](./ejemplos/crear-rule.md) |
| Permisos, roles | Activar skill `laravel-permission-development` |
| Módulo de auditoría, activity log, `LogBatch` | [auditoria.md](./auditoria.md) |
| Constantes, enums PHP, helpers de soporte | [enums-constantes.md](./enums-constantes.md) |
| Auth, guards, sanctum | [autenticacion.md](./autenticacion.md) |
| Correos, notificaciones, `FrontendUrl`, `TenantMailer` | [correos.md](./correos.md) |
| Variables de entorno | [variables-entorno.md](./variables-entorno.md) |
| Docker, servicios, red, dominios | [infraestructura.md](./infraestructura.md) |
| Contenido del sitio público (schema de páginas) | [realstate-site-content-schema.md](./realstate-site-content-schema.md) |
| Endpoint público `/api/public/company` | Ver sección [Endpoint público de empresa](#endpoint-público-de-empresa) abajo |
| Generación de PDF de documentos/contratos, plantillas Blade, logo, auto-numerado | Ver sección [Módulo DocumentPdf](#módulo-documentpdf) abajo |
| Plantillas editables de contrato (`template_sections`), preview, variables | [dominio-documentos.md](./dominio-documentos.md) — Fase 7 |
| Módulo sucursales, `ResolveBranchMiddleware`, scoping por empresa | Ver sección [Módulo Sucursales](#módulo-sucursales) abajo |
| Módulo central (dashboard, company, person, site-management) | Ver sección [Separación Central / Tenant](#separación-central--tenant) abajo |

> **Tests**: No son prioridad en este proyecto. No crear ni solicitar tests PHPUnit salvo que el usuario lo pida explícitamente.

---

## Endpoint público de empresa

`GET /api/public/company` — sin autenticación, respuesta cacheada con `CacheKeys::publicCompany()`.

**Campos que devuelve** (además de los campos de `Company`):
- `favicon_url` — leído de `RealstateSiteSetting->pages['layout']['content']['favicon_url']`

**Regla crítica de caché**: cada vez que se invalide `CacheKeys::publicRealstateSite()` también hay que invalidar `CacheKeys::publicCompany()`. Actualmente esto ocurre en `RealstateTemplateManagementService`. Si se agrega otro punto que modifique el sitio, replicar el `Cache::forget` en ambas keys.

**Implementación**: `PublicCompanyService` inyecta `IRealstateSiteSettingRepository` para leer el favicon sin exponer el endpoint de site-settings (que requiere permiso `site-settings.theme-view`).

---

## Schema de contenido del sitio (`RealstateSiteTemplates`)

El layout (`pages['layout']['content']`) incluye estos campos gestionados:

| Campo | Tipo | Descripción |
|---|---|---|
| `favicon_url` | `string\|null` | Favicon del sitio y del panel admin |
| `footer_logo_url` | `string\|null` | Logo alternativo para el footer (distinto al logo principal) |

Al agregar campos al schema de contenido de una página:
1. Añadir con valor `null` en `RealstateSiteTemplates::defaultContentForPage()`
2. Actualizar el seeder si corresponde valor inicial
3. Actualizar la interfaz TypeScript en `frontend/app/interfaces/IRealstateSiteManagement.ts`

---

## CORS

`config/cors.php` cubre estos paths: `['api/*', 'sanctum/csrf-cookie', 'storage/*']`.

El path `storage/*` aplica cuando PHP sirve los archivos (Laragon local, `php artisan serve`). En Docker producción, `nginx-storage` sirve `/storage` directamente y no pasa por el middleware de CORS — pero en producción todos los servicios comparten dominio (Traefik), por lo que no hay problema de cross-origin.

---

## Módulo DocumentPdf

`app/Services/Implements/DocumentPdfService.php` — genera PDFs para los 14 tipos de documento.

### Regla crítica — DomPDF no puede hacer HTTP

DomPDF ejecuta en el servidor sin contexto de red. **Toda imagen en las plantillas Blade debe ser una data URI base64** (`data:image/jpeg;base64,...`), nunca una URL pública. Esto aplica al logo de empresa y a cualquier imagen futura (sellos, firmas escaneadas).

```php
// Patrón para embeber imagen en DomPDF
$content = Storage::disk('public')->get($logo->file_path);
$mime    = Storage::disk('public')->mimeType($logo->file_path) ?? 'image/jpeg';
return 'data:'.$mime.';base64,'.base64_encode($content);
```

En la vista Blade:
```blade
@if(!empty($logoDataUri))
  <img src="{{ $logoDataUri }}" alt="" style="display:block;max-height:45px;max-width:160px;">
@endif
```

### Auto-numerado de contrato y documento

Si `Rent::contract_number` o `Document::number` son null al momento de generar el PDF, `DocumentPdfService` los genera automáticamente y guarda con `saveQuietly()` (sin disparar observers ni activity log).

- Formato: `YYYY-NNNN` (ej: `2026-0001`)
- Secuencia: `MAX(seq del año actual) + 1` — busca registros con patrón `YYYY-%`
- Usar MAX en lugar de COUNT para evitar colisiones si se eliminan registros intermedios

### Orden de renderizado — cláusulas antes que firmas

En `residential.blade.php` y `commercial.blade.php`, que usan el partial `dynamic-section`, las secciones se dividen en dos pasadas:

1. `$clauses->filter(fn($c) => $c->section_type !== 'signature')` — todo excepto firmas
2. Cláusulas adicionales del Rent
3. `$clauses->filter(fn($c) => $c->section_type === 'signature')` — solo firmas

Los otros 12 templates tienen firmas estáticas al final y no necesitan este patrón.

### Clases de soporte

| Clase | Responsabilidad |
|---|---|
| `DocumentTemplateMap` | Mapea `template_key` → vista Blade; maneja variantes con/sin punto |
| `TemplateSectionDefaults` | Defaults de secciones por template_key; catálogo de variables y grupos |
| `TemplateSectionService` | CRUD, reorder, reset, preview (preview usa datos fake con `logoDataUri = null`) |

---

## Regla de internacionalización (lang files)

**Nunca hardcodear strings en español en servicios o controladores.** Todos los mensajes de respuesta deben ir en `lang/es/{feature}.php` y referenciarse con `__('feature.key')`.

```php
// ❌ Incorrecto — string quemado
return response()->json(['message' => 'No se puede eliminar la plantilla.'], 422);

// ✅ Correcto — via lang
return response()->json(['message' => __('report_template.cannot_delete_default')], 422);
```

**Archivos existentes** (uno por módulo): `document.php`, `image.php`, `person.php`, `property.php`, `public_property.php`, `rent.php`, `report_template.php`, `user.php`, etc.

Al crear un módulo nuevo: crear `lang/es/{feature}.php` con las claves `created`, `updated`, `deleted` como mínimo.

---

## Módulo Sucursales

`Branch = Company` — cada sucursal es un registro `Company` con `parent_company_id` apuntando a la sede principal (HQ).

### Activación

`uses_branches` en el modelo `Company` (HQ). Cuando es `false` (default), el sistema funciona como instalación de empresa única — no hay selector de sucursal ni scoping.

### ResolveBranchMiddleware

`app/Http/Middleware/ResolveBranchMiddleware.php` — registrado como `resolve.branch` en `bootstrap/app.php`.

Aplica a: `dashboard`, `search/global`, `people`, `properties`, `rents` — tanto en `routes/api.php` (central) como en `routes/tenant.php`.

Comportamiento:
- Siempre fija `request->attributes->set('current_company_id', $companyId)` — útil para trazar la empresa en creates incluso cuando el scoping está desactivado.
- Fija `branch_scoping_active = true` solo si `uses_branches = true` Y el usuario no tiene permiso `companies.view_all`.

### resolveCompanyScope()

Método privado presente en: `PropertyService`, `PersonService`, `RentService`, `DashboardService`, `SearchService`.

```php
private function resolveCompanyScope(): ?string
{
    if (!request()->attributes->get('branch_scoping_active')) return null;
    return request()->attributes->get('current_company_id');
}
```

Devuelve `null` cuando el scoping está desactivado — los repositorios ignoran el filtro si reciben `null`.

### Pivot company_user

La tabla `company_user` ya existía dentro de la migración original `create_companies_table`. El modelo `User` expone la relación:

```php
public function companies(): BelongsToMany
{
    return $this->belongsToMany(Company::class, 'company_user')->withPivot('is_default');
}
```

`is_default` indica cuál es la sucursal por defecto del usuario al hacer login.

### Rutas (en ambos api.php y tenant.php)

```
GET    /branches          — listado de sucursales accesibles (companies.view | companies.switch)
POST   /branches          — crear sucursal (companies.create)
PUT    /branches/{id}     — editar sucursal (companies.edit)
DELETE /branches/{id}     — eliminar sucursal (companies.delete)
POST   /branch/switch     — cambiar sucursal activa (companies.switch)
GET    /branch/resolve    — retorna empresa activa del usuario
```

> `GET /branches` acepta `companies.view` **o** `companies.switch`: los agentes usan `companies.switch` para cargar su lista de sucursales en el selector sin necesitar acceso al módulo de gestión. `BranchService::index()` usa `findAccessibleForUser()` para filtrar solo las sucursales asignadas al usuario.

---

## Separación Central / Tenant

El código central y tenant evolucionan de forma independiente. Todo módulo que solo existe en el dominio central usa la carpeta `Central/`.

### Estructura de carpetas

```
app/
├── Http/Controllers/Central/    # Controladores exclusivos central
├── Services/Central/            # Interfaces + Implements/ central
└── Repositories/Central/        # Interfaces + Implements/ central
```

### Módulos separados actualmente

| Módulo | Controlador | Servicio | Repositorio |
|---|---|---|---|
| Dashboard | `Central/DashboardController` | `Central/Implements/DashboardService` | `Central/Implements/DashboardRepository` |
| SiteManagement | `Central/SiteManagementController` | `Central/Implements/SiteManagementService` | — |
| Person | `Central/PersonController` | `Central/Implements/PersonService` | `Central/Implements/PersonRepository` |
| Company | `Central/CompanyController` | `Central/Implements/CompanyService` | `Central/Implements/CompanyRepository` |
| Plan | `Central/PlanController` | `Central/Implements/PlanService` | `Central/Implements/PlanRepository` |
| Tenant | `Central/TenantController` | `Central/Implements/TenantService` | `Central/Implements/TenantRepository` |
| TenantUser | `Central/TenantUserController` | `Central/Implements/TenantUserService` | `Central/Implements/TenantUserRepository` |

### Diferencias clave vs tenant

| Aspecto | Tenant | Central |
|---|---|---|
| Resolución de empresa | `request()->attributes->get('current_company_id')` (fijado por `resolve.branch` middleware) | `Company::whereNull('parent_company_id')->value('id')` |
| Middleware de rama | `resolve.branch` aplicado en rutas | No aplica — rutas centrales no usan `resolve.branch` |
| Módulo branches | `uses_branches`, `CompanyRepository::update()` incluye ese campo | Nunca — `Central\CompanyRepository` no expone `uses_branches` |
| Propiedades | Accesibles | No existen en central |

### Regla al agregar un módulo central nuevo

1. Crear interfaz en `Services/Central/INombreService.php`
2. Crear implementación en `Services/Central/Implements/NombreService.php`
3. Si necesita repositorio: `Repositories/Central/INombreRepository.php` + `Repositories/Central/Implements/NombreRepository.php`
4. Crear controlador en `Http/Controllers/Central/NombreController.php`
5. Registrar bindings en `AppServiceProvider` (servicio) y `RepositoryServiceProvider` (repositorio) con alias `as Central*`
6. Agregar rutas en el bloque central de `routes/api.php` (sin `resolve.branch`)
7. Nunca reutilizar el servicio tenant — duplicar aunque sea idéntico, para que evolucionen independientes
