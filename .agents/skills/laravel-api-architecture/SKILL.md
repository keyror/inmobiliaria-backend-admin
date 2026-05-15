---
name: laravel-api-architecture
description: Build or refactor Laravel API features in this workspace using the project's route-to-model architecture: routes, controllers, Form Requests, language messages in lang/es, filters, repositories with interfaces, services with interfaces, models, migrations, Excel/PDF exports, bindings, and PHPUnit verification. Use for any end-to-end Laravel backend feature, CRUD module, API resource, validation flow, export, repository/service implementation, or structural review of this project's Laravel code.
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

## Architectural Rules

- Keep controllers thin. Controllers should receive route model bindings/Form Requests and delegate to service interfaces.
- Keep business orchestration in `app/Services/Implements/*Service.php`.
- Keep persistence/query concerns in `app/Repositories/Implements/*Repository.php`.
- Define interfaces in `app/Services/I{Name}Service.php` and `app/Repositories/I{Name}Repository.php`.
- Bind interfaces to implementations in `App\Providers\AppServiceProvider` for services.
- Bind interfaces to implementations in `App\Providers\RepositoryServiceProvider` for Repository.
- Centralize user-facing response text in `lang/es/{feature}.php`; avoid hardcoded Spanish messages in services/controllers.
- Prefer `$request->validated()` for new validation flows. Follow existing nested validation rule classes in `app/Validation` when the feature has reusable rule groups.
- Use `app/filter/FiltersApiQueryBuilder.php` macros for list endpoints that support `search`, `sortBy`, `sortType`, `perPage`, and `page`.
- Use transactions in services when an operation writes multiple tables or syncs relations.
- Prefer named routes in `routes/api.php`, matching the existing domain-aware route name convention.
- Add migrations with Artisan. For tenant-specific schema, inspect `database/migrations/tenant` and existing tenancy conventions first.

## Deliverables Checklist

For a complete API feature, check whether each item applies:

- Route group in `routes/api.php` or `routes/tenant.php`
- Controller in `app/Http/Controllers`
- Store/Update/Index Form Requests in `app/Http/Requests`
- Validation rule helper in `app/Validation` when rules are reused or nested
- Model in `app/Models` with fillable/guarded, casts, and relationships
- Migration in `database/migrations` or `database/migrations/tenant`
- Repository interface and implementation
- Service interface and implementation
- Interface bindings in `AppServiceProvider`
- Spanish language file in `lang/es`
- Excel export in `app/Exports/excel` when requested
- PDF export in `app/Exports/pdf` when requested
- Focused PHPUnit feature tests/factories for the changed behavior

## Response Style

When using this skill, mention which companion Laravel skills were activated and list the concrete files touched. Keep explanations short and implementation-focused.
