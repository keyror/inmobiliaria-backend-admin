# API Feature Workflow

Follow this sequence for an end-to-end Laravel API feature.

## 1. Discover Existing Patterns

- Inspect sibling controller, request, service, repository, model, migration, language, and export files.
- Use Boost `database_schema` before touching persistence design.
- Use `php artisan route:list --except-vendor` to verify route naming and collisions when routes are involved.

## 2. Routes

- Add routes in the existing grouped style.
- Prefer explicit route groups when that is the local convention:

```php
Route::prefix('examples')->name($domain.'examples.')->group(function () {
    Route::get('/', [ExampleController::class, 'index'])->name('index');
    Route::get('{example}', [ExampleController::class, 'show'])->name('show');
    Route::post('/', [ExampleController::class, 'store'])->name('store');
    Route::put('{example}', [ExampleController::class, 'update'])->name('update');
    Route::delete('{example}', [ExampleController::class, 'destroy'])->name('destroy');
});
```

- Put static routes like `export/excel` before `{model}` routes.

## 3. Controller

- Inject the service interface with constructor property promotion.
- Return `JsonResponse`.
- Delegate logic to the service.

```php
public function __construct(
    private readonly IExampleService $exampleService,
) {}
```

## 4. Requests And Validation

- Create Form Requests with Artisan.
- Keep `authorize(): bool`.
- Use `rules(): array` with array syntax.
- Put reusable/nested rules in `app/Validation/{Feature}Rules.php`.
- Prefer `$request->validated()` in new service methods.
- Put custom attribute names and messages in `lang/es/validation.php` or feature files when reusable.

## 5. Language Messages

- Add `lang/es/{feature}.php`.
- Use keys like `created`, `updated`, `deleted`, `not_found`, `exported`.
- In services, use `__('feature.key')`.
- Do not hardcode success messages in controllers or services.

## 6. Repository

- Add `app/Repositories/I{Name}Repository.php`.
- Add `app/Repositories/Implements/{Name}Repository.php`.
- Repository methods should return models, collections, paginators, or void.
- Use eager loading to avoid N+1 queries.
- Use allowed filters/sorts for index endpoints.

## 7. Service

- Add `app/Services/I{Name}Service.php`.
- Add `app/Services/Implements/{Name}Service.php`.
- Services orchestrate repositories, transactions, exports, and response shape.
- Wrap multi-write operations with `DB::beginTransaction()`, `DB::commit()`, and rollback on exceptions, or use `DB::transaction()` when the response shape stays clean.

## 8. Models And Migrations

- Create migrations with Artisan.
- Add indexes for columns used in filters, sorting, foreign keys, and unique lookups.
- Add `constrained()` foreign keys when appropriate.
- Keep model fillable/guarded and casts explicit.
- Define relationships with return types.
- Mirror database defaults in model attributes when the app depends on them.

## 9. Excel And PDF Exports

- For Excel, use `maatwebsite/excel` patterns already under `app/Exports/excel`.
- For PDF, use existing `app/Exports/pdf` and DomPDF conventions.
- Keep export query construction in repositories or export classes; keep controller methods as delegation points.
- Add routes such as `export/excel` and `export/pdf` before parameterized show routes.

## 10. Tests And Formatting

- Create PHPUnit tests with `php artisan make:test --phpunit`.
- Use factories where available.
- Cover at least successful create/update/index, validation failure, and authorization/permission behavior when relevant.
- Run the smallest relevant test file/filter.
- Run `vendor/bin/pint --dirty --format agent` after PHP changes.
