<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Services\IRoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        private readonly IRoleService $roleService
    ) {}

    /**
     * Listar roles con filtros
     * GET /roles
     */
    public function index(): JsonResponse
    {
        return $this->roleService->getRoles();
    }

    /**
     * Crear nuevo rol
     * POST /roles
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        return $this->roleService->createRole($request);
    }

    /**
     * Actualizar rol
     * PUT /roles/{role}
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        return $this->roleService->updateRole($request, $role);
    }

    /**
     * Eliminar rol
     * DELETE /roles/{role}
     */
    public function destroy(Role $role): JsonResponse
    {
        return $this->roleService->deleteRole($role);
    }

    /**
     * Asignar permisos a un rol
     * POST /roles/{role}/assign-permissions
     */
    public function assignPermissions(AssignPermissionsRequest $request, Role $role): JsonResponse
    {
        return $this->roleService->assignPermissions($request, $role);
    }
}
