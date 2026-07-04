# Ejemplo completo: módulo Contract

CRUD completo con auditoría (`LogsActivity`) y Resource Collection.

## 1. Migración

```bash
php artisan make:migration create_contracts_table
```

## 2. Model (`app/Models/Contract.php`)

```php
class Contract extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('contracts');
    }

    protected $fillable = ['property_id', 'person_id', 'start_date', 'end_date', 'status_id'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function person(): BelongsTo { return $this->belongsTo(Person::class); }
    public function status(): BelongsTo { return $this->belongsTo(Lookup::class, 'status_id'); }
}
```

## 3. Validation Rules (`app/Validation/ContractRules.php`)

```php
class ContractRules
{
    public static function store(): array
    {
        return [
            'contract.property_id' => 'required|uuid|exists:properties,id',
            'contract.person_id'   => 'required|uuid|exists:people,id',
            'contract.start_date'  => 'required|date',
            'contract.end_date'    => 'required|date|after:contract.start_date',
            'contract.status_id'   => 'required|uuid|exists:lookups,id',
        ];
    }

    public static function update(string $contractId): array
    {
        return [
            'contract.start_date' => 'sometimes|required|date',
            'contract.end_date'   => 'sometimes|required|date|after:contract.start_date',
        ];
    }
}
```

## 4. FormRequests

```php
// StoreContractRequest
public function rules(): array { return ContractRules::store(); }

// UpdateContractRequest
public function rules(): array { return ContractRules::update($this->route('contract')->id); }
```

## 5. Repository Interface (`app/Repositories/IContractRepository.php`)

```php
interface IContractRepository
{
    public function getAll(): LengthAwarePaginator;
    public function getWithRelations(Contract $contract): Contract;
    public function create(array $data): Contract;
    public function update(array $data, Contract $contract): void;
    public function delete(Contract $contract): void;
}
```

## 6. Repository Impl (`app/Repositories/Implements/ContractRepository.php`)

```php
class ContractRepository implements IContractRepository
{
    public function getAll(): LengthAwarePaginator
    {
        return Contract::query()
            ->with(['property', 'person', 'status'])
            ->allowedFilters(['property.code', 'status.alias', 'created_at'])
            ->allowedSorts(['created_at', 'start_date'])
            ->jsonPaginate();
    }

    public function getWithRelations(Contract $contract): Contract
    {
        return $contract->load(['property', 'person', 'status']);
    }

    public function create(array $data): Contract
    {
        return Contract::create($data['contract']);
    }

    public function update(array $data, Contract $contract): void
    {
        $contract->update($data['contract']);
    }

    public function delete(Contract $contract): void
    {
        $contract->delete();
    }
}
```

## 7. API Resource (`app/Http/Resources/ContractResource.php`)

```php
class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'property'   => $this->whenLoaded('property', fn () => [
                'id'   => $this->property->id,
                'code' => $this->property->code,
            ]),
            'person'     => $this->whenLoaded('person', fn () => [
                'id'   => $this->person->id,
                'name' => $this->person->full_name,
            ]),
            'status'     => $this->whenLoaded('status', fn () => [
                'id'    => $this->status->id,
                'alias' => $this->status->alias,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
```

## 8. Service Interface (`app/Services/IContractService.php`)

```php
interface IContractService
{
    public function getContracts(): JsonResponse;
    public function getContract(Contract $contract): JsonResponse;
    public function createContract(StoreContractRequest $request): JsonResponse;
    public function updateContract(UpdateContractRequest $request, Contract $contract): JsonResponse;
    public function deleteContract(Contract $contract): JsonResponse;
}
```

## 9. Service Impl — modelo único (sin LogBatch)

```php
class ContractService implements IContractService
{
    public function __construct(
        private readonly IContractRepository $contractRepository,
    ) {}

    public function getContracts(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => ContractResource::collection($this->contractRepository->getAll()),
        ]);
    }

    public function getContract(Contract $contract): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => new ContractResource($this->contractRepository->getWithRelations($contract)),
        ]);
    }

    public function createContract(StoreContractRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $contract = $this->contractRepository->create($request->validated());
            DB::commit();
            return response()->json([
                'status'  => true,
                'data'    => new ContractResource($contract),
                'message' => __('contracts.created'),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateContract(UpdateContractRequest $request, Contract $contract): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->contractRepository->update($request->validated(), $contract);
            DB::commit();
            return response()->json(['status' => true, 'message' => __('contracts.updated')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function deleteContract(Contract $contract): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->contractRepository->delete($contract);
            DB::commit();
            return response()->json(['status' => true, 'message' => __('contracts.deleted')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
```

Si el service escribe **múltiples modelos** auditados (ej: Contract + LeaseFees + Guarantors), agregar `LogBatch`:

```php
use Spatie\Activitylog\Facades\LogBatch;

public function createContract(StoreContractRequest $request): JsonResponse
{
    LogBatch::startBatch();
    DB::beginTransaction();
    try {
        // múltiples creates/updates sobre modelos con LogsActivity…
        DB::commit();
        return response()->json(['status' => true, 'message' => __('contracts.created')], 201);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
    } finally {
        LogBatch::endBatch();
    }
}
```

## 10. Controller (`app/Http/Controllers/ContractController.php`)

```php
class ContractController extends Controller
{
    public function __construct(private readonly IContractService $contractService) {}

    public function index(): JsonResponse { return $this->contractService->getContracts(); }
    public function show(Contract $contract): JsonResponse { return $this->contractService->getContract($contract); }
    public function store(StoreContractRequest $request): JsonResponse { return $this->contractService->createContract($request); }
    public function update(UpdateContractRequest $request, Contract $contract): JsonResponse { return $this->contractService->updateContract($request, $contract); }
    public function destroy(Contract $contract): JsonResponse { return $this->contractService->deleteContract($contract); }
}
```

## 11. Bindings

```php
// AppServiceProvider::boot()
$this->app->bind(IContractService::class, ContractService::class);

// RepositoryServiceProvider::boot()
$this->app->bind(IContractRepository::class, ContractRepository::class);
```

## 12. Ruta (`routes/api.php`)

```php
Route::prefix('contracts')->name($domain.'contracts.')->group(function () {
    Route::get('export/excel', [ContractController::class, 'exportExcel'])->name('export.excel');
    Route::get('/', [ContractController::class, 'index'])->name('index');
    Route::post('/', [ContractController::class, 'store'])->name('store');
    Route::get('{contract}', [ContractController::class, 'show'])->name('show');
    Route::put('{contract}', [ContractController::class, 'update'])->name('update');
    Route::delete('{contract}', [ContractController::class, 'destroy'])->name('destroy');
});
```

## 13. Mensajes (`lang/es/contracts.php`)

```php
return [
    'created' => 'Contrato creado correctamente.',
    'updated' => 'Contrato actualizado correctamente.',
    'deleted' => 'Contrato eliminado correctamente.',
];
```

## 14. Verificación

```bash
vendor/bin/pint --dirty --format agent
php artisan route:list --except-vendor | grep contract
```
