# BACKEND — Guía para Claude

## Contexto
- **Stack**: Laravel 11 + Spatie Query Builder + Spatie Permission + Boost
- **Skills**: `laravel-api-architecture` (principal), `laravel-best-practices`, `laravel-permission-development`
- **Ubicación**: `/backend`

---

## Cuándo leer qué

| Tarea | Leer primero |
|---|---|
| Crear feature completo (CRUD) | [arquitectura.md](./arquitectura.md) + [ejemplos/crear-servicio.md](./ejemplos/crear-servicio.md) |
| Agregar validación | [arquitectura.md](./arquitectura.md#validación) + [ejemplos/crear-rule.md](./ejemplos/crear-rule.md) |
| Permisos, roles | Activar skill `laravel-permission-development` |
| Constantes, enums PHP | [enums-constantes.md](./enums-constantes.md) |
| Auth, guards, sanctum | [autenticacion.md](./autenticacion.md) |
| Variables de entorno | [variables-entorno.md](./variables-entorno.md) |
| Docker, servicios, red, dominios | [infraestructura.md](./infraestructura.md) |
| Contenido sitio público | [realstate-site-content-schema.md](./realstate-site-content-schema.md) |

---

## Estructura de carpetas clave

```
app/
├── Http/
│   ├── Controllers/          # Thin: solo delega al service
│   │   └── Public/           # Controladores sin auth
│   └── Requests/             # StoreXxxRequest, UpdateXxxRequest
├── Services/
│   ├── IXxxService.php       # Interface
│   └── Implements/
│       └── XxxService.php    # Implementación
├── Repositories/
│   ├── IXxxRepository.php    # Interface
│   └── Implements/
│       └── XxxRepository.php # Implementación
├── Models/                   # Eloquent (singular: Property, Person...)
├── Validation/               # XxxRules.php (static store/update)
├── Exports/
│   ├── excel/                # Maatwebsite Excel
│   └── pdf/                  # DomPDF
├── Support/                  # Helpers: CacheKeys, CalculateDV
├── Providers/
│   ├── AppServiceProvider    # Bindings de services
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
    'data' => $data,
    'message' => __('feature.created'),
]);

// Error (400)
return response()->json([
    'status' => false,
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
- [ ] Model en `app/Models/`
- [ ] Migración
- [ ] Bindings en `AppServiceProvider` y `RepositoryServiceProvider`
- [ ] Mensajes en `lang/es/{feature}.php`

> **Tests**: No son prioridad en este proyecto. No crear ni solicitar tests PHPUnit salvo que el usuario lo pida explícitamente.
