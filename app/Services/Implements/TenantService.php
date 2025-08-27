<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Repositories\ITenantRepository;
use App\Services\ITenantService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class TenantService implements ITenantService
{
    public function __construct(
        private readonly ITenantRepository $tenantRepository
    ) {}

    public function getTenants(): JsonResponse
    {
        try {
            $tenants = $this->tenantRepository->getTenantsByFilters();
            return response()->json([
                'status' => true,
                'data' => $tenants,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getTenant(Tenant $tenant): JsonResponse
    {
        try {
            $tenant = $this->tenantRepository->getTenant($tenant);
            return response()->json([
                'status' => true,
                'data' => $tenant,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function createTenant(StoreTenantRequest $request): JsonResponse
    {
        try {

            $this->tenantRepository->create($request);

            return response()->json([
                'status' => true,
                'message' => [__('tenant.created')]
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function updateTenant(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        try {

            if ($request->has('domain') && $request->domain !== $tenant->domain) {
                $tenant->domains()->delete();
            }

            $this->tenantRepository->update($request, $tenant);

            return response()->json([
                'status' => true,
                'message' => [__('tenant.updated')]
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteTenant(Tenant $tenant): JsonResponse
    {
        try {

            $this->tenantRepository->delete($tenant);

            return response()->json([
                'status' => true,
                'message' => [__('tenant.deleted')]
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function activateTenant(Tenant $tenant): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->tenantRepository->activate($tenant);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('tenant.activated')]
            ], 201);

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function deactivateTenant(Tenant $tenant): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->tenantRepository->deactivate($tenant);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('tenant.deactivated')]
            ], 201);

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
