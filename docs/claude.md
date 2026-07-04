# BACKEND — Guía para Claude

## Contexto
- **Stack**: Laravel 11 + Spatie Query Builder + Spatie Permission + Spatie Activitylog + Boost
- **Skills**: `laravel-api-architecture` (principal), `laravel-best-practices`, `laravel-permission-development`
- **Ubicación**: `backend/`

---

## Cuándo leer qué

| Tarea | Leer primero |
|---|---|
| Crear feature completo (CRUD) | Activar skill `laravel-api-architecture` |
| Agregar validación | Activar skill `laravel-api-architecture` + [ejemplos/crear-rule.md](./ejemplos/crear-rule.md) |
| Permisos, roles | Activar skill `laravel-permission-development` |
| Módulo de auditoría, activity log, `LogBatch` | [auditoria.md](./auditoria.md) |
| Constantes, enums PHP, helpers de soporte | [enums-constantes.md](./enums-constantes.md) |
| Auth, guards, sanctum | [autenticacion.md](./autenticacion.md) |
| Variables de entorno | [variables-entorno.md](./variables-entorno.md) |
| Docker, servicios, red, dominios | [infraestructura.md](./infraestructura.md) |
| Contenido del sitio público (schema de páginas) | [realstate-site-content-schema.md](./realstate-site-content-schema.md) |
| Endpoint público `/api/public/company` | Ver sección [Endpoint público de empresa](#endpoint-público-de-empresa) abajo |

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
