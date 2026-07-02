# BACKEND — Guía para Claude

## Contexto
- **Stack**: Laravel 11 + Spatie Query Builder + Spatie Permission + Spatie Activitylog + Boost
- **Skills**: `laravel-api-architecture` (principal), `laravel-best-practices`, `laravel-permission-development`
- **Ubicación**: `backend/`

---

## Cuándo leer qué

| Tarea | Leer primero |
|---|---|
| Crear feature completo (CRUD) | [arquitectura.md](./arquitectura.md) + [ejemplos/crear-servicio.md](./ejemplos/crear-servicio.md) |
| Agregar validación | [arquitectura.md](./arquitectura.md) + [ejemplos/crear-rule.md](./ejemplos/crear-rule.md) |
| Permisos, roles | Activar skill `laravel-permission-development` |
| Módulo de auditoría, activity log, `LogBatch` | [auditoria.md](./auditoria.md) |
| Constantes, enums PHP, helpers de soporte | [enums-constantes.md](./enums-constantes.md) |
| Auth, guards, sanctum | [autenticacion.md](./autenticacion.md) |
| Variables de entorno | [variables-entorno.md](./variables-entorno.md) |
| Docker, servicios, red, dominios | [infraestructura.md](./infraestructura.md) |
| Contenido del sitio público (schema de páginas) | [realstate-site-content-schema.md](./realstate-site-content-schema.md) |

---

## Estructura de carpetas clave

```
app/
├── Http/
│   ├── Controllers/          # Thin: solo delega al service
│   │   └── Public/           # Controladores sin auth (sitio público)
│   ├── Requests/             # StoreXxxRequest, UpdateXxxRequest
│   └── Resources/            # XxxResource (JsonResource)
├── Services/
│   ├── IXxxService.php       # Interface
│   └── Implements/
│       └── XxxService.php    # Implementación (orquesta, transacciones)
├── Repositories/
│   ├── IXxxRepository.php    # Interface
│   └── Implements/
│       └── XxxRepository.php # Implementación (queries Eloquent)
├── Models/                   # Eloquent (singular: Property, Person…)
├── Validation/               # XxxRules.php (static store/update)
├── Exports/
│   ├── excel/                # Maatwebsite Excel
│   └── pdf/                  # DomPDF
├── Support/                  # Clases de soporte (ver enums-constantes.md)
│   ├── AuditValueResolver.php
│   ├── CacheKeys.php
│   ├── CalculateDV.php
│   ├── RealstateSiteTemplates.php
│   ├── TextCaseResolver.php
│   └── TextCaseTransformer.php
├── Providers/
│   ├── AppServiceProvider        # Bindings de services + Gate::before para super admin
│   └── RepositoryServiceProvider # Bindings de repos
└── filter/
    └── FiltersApiQueryBuilder.php # Macros: allowedFilters/Sorts/jsonPaginate
```

---

## Patrón de respuesta estándar

```php
// Éxito
return response()->json([
    'status' => true,
    'data'   => $data,
    'message' => __('feature.created'),
]);

// Error (400)
return response()->json([
    'status'  => false,
    'message' => $e->getMessage(),
], 400);
```

---

## Checklist para feature completo

- [ ] Ruta en `routes/api.php` (agrupada, con nombre)
- [ ] Controller en `app/Http/Controllers/`
- [ ] FormRequest: `Store` + `Update` en `app/Http/Requests/`
- [ ] Reglas de validación en `app/Validation/XxxRules.php`
- [ ] Interface de servicio: `app/Services/IXxxService.php`
- [ ] Implementación: `app/Services/Implements/XxxService.php`
- [ ] Interface de repo: `app/Repositories/IXxxRepository.php`
- [ ] Implementación: `app/Repositories/Implements/XxxRepository.php`
- [ ] Model en `app/Models/` con `LogsActivity` si aplica
- [ ] Migración
- [ ] Bindings en `AppServiceProvider` y `RepositoryServiceProvider`
- [ ] Mensajes en `lang/es/{feature}.php`
- [ ] Si el service escribe múltiples modelos: envolver con `LogBatch::startBatch()/endBatch()`

> **Tests**: No son prioridad en este proyecto. No crear ni solicitar tests PHPUnit salvo que el usuario lo pida explícitamente.
