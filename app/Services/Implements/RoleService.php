<?php

namespace App\Services\Implements;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\IRoleRepository;
use App\Services\IRoleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleService implements IRoleService
{
    public function __construct(
        private readonly IRoleRepository $roleRepository
    ) {}

    public function getRoles(): JsonResponse
    {
        try {
            $roles = $this->roleRepository->getRolesByFilters();
            return response()->json([
                'status' => true,
                'data' => $roles,
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
    public function createRole(StoreRoleRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->roleRepository->create($request);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('rol.created')]
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
    public function updateRole(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->roleRepository->update($request, $role);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('rol.updated')]
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
    public function deleteRole(Role $role): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->roleRepository->delete($role);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('rol.deleted')]
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
    public function assignPermissions(AssignPermissionsRequest $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->roleRepository->assignPermissions($request, $role);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('permission.assigned')]
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
