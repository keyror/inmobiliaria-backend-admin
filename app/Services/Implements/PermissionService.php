<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Repositories\IPermissionRepository;
use App\Services\IPermissionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Throwable;

class PermissionService implements IPermissionService
{
    public function __construct(
        private readonly IPermissionRepository $permissionRepository
    ) {}

    public function getPermissions(): JsonResponse
    {
        try {
            $permissions = $this->permissionRepository->getPermissionsByFilters();
            return response()->json([
                'status' => true,
                'data' => $permissions,
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
    public function createPermission(StorePermissionRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->permissionRepository->create($request);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Permiso creado exitosamente']
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
    public function updatePermission(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->permissionRepository->update($request, $permission);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Permiso actualizado exitosamente']
            ], 200);

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
    public function deletePermission(Permission $permission): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->permissionRepository->delete($permission);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Permiso eliminado exitosamente']
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
