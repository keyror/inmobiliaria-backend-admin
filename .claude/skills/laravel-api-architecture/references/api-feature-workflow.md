# API Feature Workflow

Follow this sequence for an end-to-end Laravel API feature.

## Architecture Flow

```
routes/api.php
    → Controller (thin — solo delega)
        → IXxxService (interface)
            → XxxService (orquesta, transacciones, respuesta JSON)
                → XxxResource / XxxResource::collection()
                → IXxxRepository (interface)
                    → XxxRepository (queries Eloquent)
                        → Model
```

Validation:
```
HTTP Request
    → FormRequest (StoreXxxRequest / UpdateXxxRequest)
        → rules(): array_merge(XxxRules::store(), OtherRules::store(), ...)
            → app/Validation/XxxRules.php (static: store(), update($id))
```

## Standard Response Pattern

```php
// Éxito (index — colección paginada)
return response()->json([
    'status' => true,
    'data'   => ExampleResource::collection($paginator),
]);

// Éxito (store — 201)
return response()->json([
    'status'  => true,
    'data'    => new ExampleResource($model),
    'message' => __('examples.created'),
], 201);

// Éxito (update/delete)
return response()->json([
    'status'  => true,
    'message' => __('examples.updated'),
]);

// Error (400)
return response()->json([
    'status'  => false,
    'message' => $e->getMessage(),
], 400);
```

---

## 1. Discover Existing Patterns

- Inspect sibling controller, request, service, repository, model, migration, language, and export files.
- Use Boost `database_schema` before touching persistence design.
- Use `php artisan route:list --except-vendor` to verify route naming and collisions when routes are involved.

## 2. Routes

- Add routes in the existing grouped style.
- Put static routes like `export/excel` before `{model}` routes.

```php
Route::prefix('examples')->name($domain.'examples.')->group(function () {
    Route::get('export/excel', [ExampleController::class, 'exportExcel'])->name('export.excel');
    Route::get('/', [ExampleController::class, 'index'])->name('index');
    Route::get('{example}', [ExampleController::class, 'show'])->name('show');
    Route::post('/', [ExampleController::class, 'store'])->name('store');
    Route::put('{example}', [ExampleController::class, 'update'])->name('update');
    Route::delete('{example}', [ExampleController::class, 'destroy'])->name('destroy');
});
```

## 3. Controller

- Inject the service interface with constructor property promotion.
- Return `JsonResponse`.
- Delegate all logic to the service — no business logic in the controller.

```php
class ExampleController extends Controller
{
    public function __construct(
        private readonly IExampleService $exampleService,
    ) {}

    public function index(): JsonResponse { return $this->exampleService->getExamples(); }
    public function show(Example $example): JsonResponse { return $this->exampleService->getExample($example); }
    public function store(StoreExampleRequest $request): JsonResponse { return $this->exampleService->createExample($request); }
    public function update(UpdateExampleRequest $request, Example $example): JsonResponse { return $this->exampleService->updateExample($request, $example); }
    public function destroy(Example $example): JsonResponse { return $this->exampleService->deleteExample($example); }
}
```

## 4. Requests And Validation

- Create Form Requests with Artisan.
- Keep `authorize(): bool`.
- Use `rules(): array` with array syntax.
- Put reusable/nested rules in `app/Validation/{Feature}Rules.php`.
- Prefer `$request->validated()` in service methods.
- Put custom attribute names and messages in `lang/es/validation.php` or feature files when reusable.

```php
class StoreExampleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return array_merge(
            ExampleRules::store(),
            AddressRules::store(), // si aplica
        );
    }
}
```

Validation rule class:

```php
// app/Validation/ExampleRules.php
class ExampleRules
{
    public static function store(): array
    {
        return [
            'example.name'      => 'required|string|max:255',
            'example.status_id' => 'required|uuid|exists:lookups,id',
        ];
    }

    public static function update(string $exampleId): array
    {
        return [
            'example.name'      => 'sometimes|required|string|max:255',
            'example.status_id' => ['sometimes', 'required', 'uuid', Rule::exists('lookups', 'id')],
        ];
    }
}
```

## 5. Language Messages

- Add `lang/es/{feature}.php`.
- Use keys like `created`, `updated`, `deleted`, `not_found`, `exported`.
- In services, use `__('feature.key')`.
- Do not hardcode success messages in controllers or services.

```php
return [
    'created'   => 'Ejemplo creado correctamente.',
    'updated'   => 'Ejemplo actualizado correctamente.',
    'deleted'   => 'Ejemplo eliminado correctamente.',
    'not_found' => 'Ejemplo no encontrado.',
];
```

## 6. Repository

- Add `app/Repositories/I{Name}Repository.php`.
- Add `app/Repositories/Implements/{Name}Repository.php`.
- Repository methods return models, collections, paginators, or void.
- Use eager loading to avoid N+1 queries.
- Para filtros/sorts de usuario: preferir `allowedFilters()` + `allowedSorts()` + `jsonPaginate()`. Si el filtro requiere lógica que los macros no cubren, usar una clase Filter personalizada de Spatie. `where` manual sobre parámetros del usuario es el último recurso y debe ir acompañado de un comentario. `where` sin comentario solo para constraints internos fijos (tenant scope, etc.).

```php
interface IExampleRepository
{
    public function getAll(): LengthAwarePaginator;
    public function getWithRelations(Example $example): Example;
    public function create(array $data): Example;
    public function update(array $data, Example $example): void;
    public function delete(Example $example): void;
}
```

```php
class ExampleRepository implements IExampleRepository
{
    public function getAll(): LengthAwarePaginator
    {
        return Example::query()
            ->with(['status', 'type'])
            ->allowedFilters(['name', 'status.alias', 'created_at'])
            ->allowedSorts(['name', 'created_at'])
            ->jsonPaginate();
    }

    public function create(array $data): Example
    {
        return Example::create($data['example']);
    }
}
```

## 7. API Resources

- Create `app/Http/Resources/{Name}Resource.php` extending `JsonResource`.
- Use `new {Name}Resource($model)` for single records.
- Use `{Name}Resource::collection($paginator)` for index/list responses.
- Resources transform the model before it reaches the JSON response — never return raw Eloquent models in the data key.

```php
// app/Http/Resources/ExampleResource.php
class ExampleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'status'     => $this->whenLoaded('status', fn () => [
                'id'    => $this->status->id,
                'alias' => $this->status->alias,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
```

## 8. Service

- Add `app/Services/I{Name}Service.php`.
- Add `app/Services/Implements/{Name}Service.php`.
- Services orchestrate repositories, build Resources, and shape JSON responses.
- Wrap multi-write operations with `DB::transaction()` or `DB::beginTransaction()/commit()/rollBack()`.

### Service de modelo único

```php
class ExampleService implements IExampleService
{
    public function __construct(
        private readonly IExampleRepository $exampleRepository,
    ) {}

    public function getExamples(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => ExampleResource::collection($this->exampleRepository->getAll()),
        ]);
    }

    public function createExample(StoreExampleRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $example = $this->exampleRepository->create($request->validated());
            DB::commit();
            return response()->json([
                'status'  => true,
                'data'    => new ExampleResource($example),
                'message' => __('examples.created'),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
```

### Service multi-modelo (con LogBatch para auditoría)

Usar `LogBatch` solo cuando el service escribe **más de un modelo auditado** con `LogsActivity`. Agrupa todos los logs bajo el mismo `batch_uuid` para que el frontend los muestre como una sola operación.

```php
use Spatie\Activitylog\Facades\LogBatch;

public function createExample(StoreExampleRequest $request): JsonResponse
{
    LogBatch::startBatch();
    DB::beginTransaction();
    try {
        // múltiples creates/updates sobre modelos con LogsActivity…
        DB::commit();
        return response()->json([
            'status'  => true,
            'message' => __('examples.created'),
        ], 201);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
    } finally {
        LogBatch::endBatch(); // siempre se ejecuta, incluso en excepción
    }
}
```

## 9. Models And Migrations

- Create migrations with Artisan.
- Add indexes for columns used in filters, sorting, foreign keys, and unique lookups.
- Add `constrained()` foreign keys when appropriate.
- Keep model fillable/guarded and casts explicit.
- Define relationships with return types.

```php
class Example extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('examples');
    }

    protected $fillable = ['name', 'status_id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }
}
```

## 10. Bindings

```php
// AppServiceProvider::boot()
$this->app->bind(IExampleService::class, ExampleService::class);

// RepositoryServiceProvider::boot()
$this->app->bind(IExampleRepository::class, ExampleRepository::class);
```

## 11. Excel And PDF Exports

- For Excel, use `maatwebsite/excel` patterns already under `app/Exports/excel`.
- For PDF, use existing `app/Exports/pdf` and DomPDF conventions.
- Add routes such as `export/excel` before parameterized show routes.

## 12. Formatting

- Run `vendor/bin/pint --dirty --format agent` after PHP changes.
- Run `php artisan route:list --except-vendor | grep {feature}` to verify routes.

> See `references/example-contract.md` for a complete end-to-end implementation example.
