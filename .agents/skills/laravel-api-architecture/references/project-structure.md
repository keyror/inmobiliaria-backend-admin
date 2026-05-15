# Project Structure

Use the existing structure before introducing new folders.

## Application Folders

- `routes/api.php`: main API routes. Existing routes are grouped by tenant/domain and often use `Route::prefix(...)->name($domain.'feature.')->group(...)`.
- `routes/tenant.php`: tenant route definitions. Inspect before adding tenant-only behavior.
- `app/Http/Controllers`: thin controllers that delegate to service interfaces.
- `app/Http/Requests`: Form Request validation classes such as `StorePersonRequest`, `UpdatePersonRequest`, and index/filter requests.
- `app/Validation`: reusable validation rule groups for nested payloads and repeated validation concerns.
- `app/Models`: Eloquent models, casts, relationships, route model binding targets.
- `app/filter/FiltersApiQueryBuilder.php`: query builder macros for API filtering, sorting, and pagination.
- `app/Repositories`: repository interfaces named `I{Name}Repository.php`.
- `app/Repositories/Implements`: repository implementations named `{Name}Repository.php`.
- `app/Services`: service interfaces named `I{Name}Service.php`.
- `app/Services/Implements`: service implementations named `{Name}Service.php`.
- `app/Exports/excel`: Maatwebsite Excel exports.
- `app/Exports/pdf`: PDF export classes, currently backed by `barryvdh/laravel-dompdf`.
- `lang/es`: Spanish PHP language files for feature response messages and validation labels.
- `database/migrations`: central migrations.
- `database/migrations/tenant`: tenant-specific migrations.

## Naming

- Use singular model names: `Person`, `Property`, `Tenant`.
- Use plural route prefixes: `people`, `properties`, `tenants`.
- Use interface prefixes already present in the project: `IPersonService`, `IPersonRepository`.
- Use implementation namespaces:
  - `App\Services\Implements`
  - `App\Repositories\Implements`
- Use language files by feature domain: `people.php`, `user.php`, `tenant.php`.

## Bindings

Register new interface bindings in `App\Providers\AppServiceProvider::boot()`:

```php
$this->app->bind(IExampleService::class, ExampleService::class);
$this->app->bind(IExampleRepository::class, ExampleRepository::class);
```

Import both the interface and implementation. Keep bindings grouped with similar services/repositories.

## Filters

For list endpoints, follow the existing macro style:

```php
return Model::query()
    ->with(['relation'])
    ->allowedFilters(['name', 'relation.alias', 'created_at'])
    ->allowedSorts(['name', 'relation.alias', 'created_at'])
    ->jsonPaginate();
```

Validate or whitelist allowed query fields in the request/repository. Avoid accepting arbitrary column names.
