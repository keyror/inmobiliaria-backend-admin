<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreRentRequest;
use App\Http\Requests\UpdateRentRequest;
use App\Models\Rent;
use App\Repositories\IRentRepository;
use App\Services\IRentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\LogBatch;

class RentService implements IRentService
{
    public function __construct(
        private readonly IRentRepository $rentRepository
    ) {}

    public function getRents(): JsonResponse
    {
        try {
            $rents = $this->rentRepository->getRentsByFilters();

            return response()->json([
                'status' => true,
                'data' => $rents,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getRent(Rent $rent): JsonResponse
    {
        try {
            $rentData = $this->rentRepository->getRentWithRelations($rent);

            return response()->json([
                'status' => true,
                'data' => $rentData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createRent(StoreRentRequest $request): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $rent = $this->rentRepository->create($requestData['rent']);

            if (! empty($requestData['rent_tenants'])) {
                $rent->rentTenantCodebtors()->createMany(
                    array_map(
                        fn (array $row) => [
                            'tenant_id' => $row['tenant_id'],
                            'codebtor_id' => $row['codebtor_id'] ?? null,
                        ],
                        $requestData['rent_tenants']
                    )
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Contrato creado exitosamente'],
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        } finally {
            LogBatch::endBatch();
        }
    }

    public function updateRent(UpdateRentRequest $request, Rent $rent): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $this->rentRepository->update($requestData['rent'] ?? [], $rent);

            if (isset($requestData['rent_tenants'])) {
                $rent->rentTenantCodebtors()->delete();
                if (! empty($requestData['rent_tenants'])) {
                    $rent->rentTenantCodebtors()->createMany(
                        array_map(
                            fn (array $row) => [
                                'tenant_id' => $row['tenant_id'],
                                'codebtor_id' => $row['codebtor_id'] ?? null,
                            ],
                            $requestData['rent_tenants']
                        )
                    );
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Contrato actualizado exitosamente'],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        } finally {
            LogBatch::endBatch();
        }
    }

    public function deleteRent(Rent $rent): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->rentRepository->delete($rent);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Contrato eliminado exitosamente'],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
