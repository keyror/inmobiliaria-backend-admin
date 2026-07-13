---
name: laravel-api-architecture
description: "Build or refactor Laravel API features in this workspace using the project's route-to-model architecture: routes, controllers, Form Requests, language messages in lang/es, filters, repositories with interfaces, services with interfaces, models, migrations, Excel/PDF exports, bindings, and PHPUnit verification. Use for any end-to-end Laravel backend feature, CRUD module, API resource, validation flow, export, repository/service implementation, or structural review of this project's Laravel code."
---

# Laravel API Architecture

Use this skill when implementing Laravel backend features in this project. It complements, not replaces, Laravel Boost skills and docs.

## Required Order

1. Use Laravel Boost `application_info` and `search_docs` before code changes.
2. Activate `laravel-best-practices` for Laravel code, and `laravel-permission-development` when roles, permissions, policies, or Spatie middleware are involved.
3. Inspect sibling files before creating or changing a pattern.
4. Use Artisan generators where practical, then adapt to this project's structure.
5. Run `vendor/bin/pint --dirty --format agent` after PHP edits and the smallest relevant PHPUnit test.

## Project References

- Read `references/project-structure.md` before adding or moving application files.
- Read `references/api-feature-workflow.md` before implementing a CRUD/API feature or export.
- See `references/example-contract.md` for a complete end-to-end implementation reference.

## Architectural Rules

- Keep controllers thin. Controllers should receive route model bindings/Form Requests and delegate to service interfaces.
- Keep business orchestration in `app/Services/Implements/*Service.php`.
- Keep persistence/query concerns in `app/Repositories/Implements/*Repository.php`.
- Define interfaces in `app/Services/I{Name}Service.php` and `app/Repositories/I{Name}Repository.php`.
- Bind interfaces to implementations in `App\Providers\AppServiceProvider` for services.
- Bind interfaces to implementations in `App\Providers\RepositoryServiceProvider` for Repository.
- Centralize user-facing response text in `lang/es/{feature}.php`; avoid hardcoded Spanish messages in services/controllers.
- Prefer `$request->validated()` for new validation flows. Follow existing nested validation rule classes in `app/Validation` when the feature has reusable rule groups.
- **List endpoints deben usar `allowedFilters()` / `allowedSorts()` / `jsonPaginate()` de `app/filter/FiltersApiQueryBuilder.php`.** Para filtros complejos que los macros no soporten nativamente, crear una clase Filter personalizada de Spatie Query Builder antes de recurrir a `where` manual. El `where` manual sobre parámetros del usuario es el último recurso; si se usa, agregar un comentario explicando por qué los macros no eran suficientes. El `where` sin comentario está reservado para constraints internos fijos (ej: `where('tenant_id', $tenantId)`).
- Use transactions in services when an operation writes multiple tables or syncs relations.
- Prefer named routes in `routes/api.php`, matching the existing domain-aware route name convention.
- Add migrations with Artisan. For tenant-specific schema, inspect `database/migrations/tenant` and existing tenancy conventions first.

## Deliverables Checklist

For a complete API feature, check whether each item applies:

- Route group in `routes/api.php` or `routes/tenant.php`
- Controller in `app/Http/Controllers`
- Store/Update/Index Form Requests in `app/Http/Requests`
- Validation rule helper in `app/Validation` when rules are reused or nested
- API Resource in `app/Http/Resources` (always — use for single and collection responses)
- Model in `app/Models` with fillable/guarded, casts, and relationships
- Migration in `database/migrations` or `database/migrations/tenant`
- Repository interface and implementation
- Service interface and implementation
- Interface bindings in `AppServiceProvider`
- Spanish language file in `lang/es`
- Excel export in `app/Exports/excel` when requested
- PDF export in `app/Exports/pdf` when requested
- Focused PHPUnit feature tests/factories for the changed behavior

## Tipos de datos — alineación migración ↔ validación ↔ frontend — regla obligatoria

La columna de la migración, la regla de validación en `RentRules.php` / `XxxRules.php` y el tipo que envía el frontend deben coincidir exactamente. Un mismatch produce errores de validación tipo "debe ser una cadena de caracteres" cuando el front envía un número.

| Tipo en migración | Regla Laravel | Yup frontend | Input frontend |
|---|---|---|---|
| `decimal(X,Y)` / `numeric` | `numeric\|min:0` | `Yup.number()` | `NumberField` |
| `integer` | `integer\|min:X` | `Yup.number().integer()` | `NumberField` |
| `boolean` / `tinyint(1)` | `boolean` | `Yup.boolean()` | `Checkbox` |
| `varchar` / `text` | `string\|max:X` | `Yup.string()` | `Textfield` / `Textarea` |
| `date` | `date` | `Yup.string()` | `VueDatePicker` |
| `char(36)` FK nullable | `nullable\|uuid\|exists:table,id` | `Yup.string().nullable()` | `Selectfield` |
| `json` | `array` | `Yup.array()` | array dinámico |

### Middleware `ConvertEmptyStringsToNull`

El proyecto tiene `ConvertEmptyStringsToNull` registrado globalmente en `bootstrap/app.php`. Convierte automáticamente todos los `""` del request a `null` antes de la validación. Esto resuelve:
- `SelectField` vacío que emite `""` en lugar de `null` para FKs opcionales
- Campos de texto opcionales que envían `""` en lugar de `null`
- Campos `date` que reciben `""` y causarían error al guardar en MySQL

**Por lo tanto:** las reglas `sometimes|nullable|uuid` y `sometimes|nullable|date` funcionan correctamente para campos opcionales vacíos sin necesidad de lógica adicional en el repositorio.

### Al crear o modificar un campo numérico en migración

1. Usar `$table->decimal('campo', 5, 2)` para porcentajes y tasas (no `string`)
2. Actualizar la regla en `XxxRules.php` a `numeric|min:0` (no `string`)
3. Verificar que el frontend use `Yup.number()` y `NumberField` para ese campo
4. Si el campo existía como `string` en la DB, crear migración `->change()` con limpieza previa de valores no numéricos

## Response Style

When using this skill, mention which companion Laravel skills were activated and list the concrete files touched. Keep explanations short and implementation-focused.
