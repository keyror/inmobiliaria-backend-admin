# Arquitectura Backend — Laravel 11

## Flujo completo de una feature

```
routes/api.php
    → Controller (thin — solo delega)
        → IXxxService (interface)
            → XxxService (orquesta, transacciones, respuesta JSON)
                → IXxxRepository (interface)
                    → XxxRepository (queries Eloquent)
                        → Model
```

## Validación

```
HTTP Request
    → FormRequest (StoreXxxRequest / UpdateXxxRequest)
        → rules(): array_merge(XxxRules::store(), AddressRules::store(), ...)
            → app/Validation/XxxRules.php (static: store(), update($id))
```

---

## 1. Ruta (`routes/api.php`)

```php
Route::prefix('examples')->name($domain.'examples.')->group(function () {
    Route::get('/', [ExampleController::class, 'index'])->name('index');
    Route::get('{example}', [ExampleController::class, 'show'])->name('show');
    Route::post('/', [ExampleController::class, 'store'])->name('store');
    Route::put('{example}', [ExampleController::class, 'update'])->name('update');
    Route::delete('{example}', [ExampleController::class, 'destroy'])->name('destroy');
    // SIEMPRE rutas estáticas antes de {example}:
    Route::get('export/excel', [ExampleController::class, 'exportExcel'])->name('export.excel');
});
```

## 2. Controller

```php
class ExampleController extends Controller
{
    public function __construct(
        private readonly IExampleService $exampleService
    ) {}

    public function index(): JsonResponse
    {
        return $this->exampleService->getExamples();
    }

    public function store(StoreExampleRequest $request): JsonResponse
    {
        return $this->exampleService->createExample($request);
    }
}
```

## 3. FormRequest

```php
class StoreExampleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return array_merge(
            ExampleRules::store(),
            AddressRules::store(),   // si aplica
        );
    }
}
```

## 4. Validation Rules

```php
// app/Validation/ExampleRules.php
class ExampleRules
{
    public static function store(): array
    {
        return [
            'example.name' => 'required|string|max:255',
            'example.status_id' => 'required|uuid|exists:lookups,id',
        ];
    }

    public static function update(string $exampleId): array
    {
        return [
            'example.name' => 'sometimes|required|string|max:255',
            'example.status_id' => [
                'sometimes', 'required', 'uuid',
                Rule::exists('lookups', 'id'),
            ],
        ];
    }
}
```

## 5. Service Interface

```php
// app/Services/IExampleService.php
interface IExampleService
{
    public function getExamples(): JsonResponse;
    public function getExample(Example $example): JsonResponse;
    public function createExample(StoreExampleRequest $request): JsonResponse;
    public function updateExample(UpdateExampleRequest $request, Example $example): JsonResponse;
    public function deleteExample(Example $example): JsonResponse;
}
```

## 6. Service Implementation

```php
// app/Services/Implements/ExampleService.php
class ExampleService implements IExampleService
{
    public function __construct(
        private readonly IExampleRepository $exampleRepository
    ) {}

    public function getExamples(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->exampleRepository->getAll(),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createExample(StoreExampleRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $example = $this->exampleRepository->create($request->validated());
            DB::commit();
            return response()->json([
                'status' => true,
                'data' => $example,
                'message' => __('examples.created'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
```

## 7. Repository Interface

```php
// app/Repositories/IExampleRepository.php
interface IExampleRepository
{
    public function getAll(): LengthAwarePaginator;
    public function getWithRelations(Example $example): Example;
    public function create(array $data): Example;
    public function update(array $data, Example $example): void;
    public function delete(Example $example): void;
}
```

## 8. Repository Implementation

```php
// app/Repositories/Implements/ExampleRepository.php
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

## 9. Bindings

```php
// AppServiceProvider::boot()
$this->app->bind(IExampleService::class, ExampleService::class);

// RepositoryServiceProvider::boot()
$this->app->bind(IExampleRepository::class, ExampleRepository::class);
```

## 10. Mensajes (`lang/es/examples.php`)

```php
return [
    'created' => 'Ejemplo creado correctamente.',
    'updated' => 'Ejemplo actualizado correctamente.',
    'deleted' => 'Ejemplo eliminado correctamente.',
    'not_found' => 'Ejemplo no encontrado.',
];
```
