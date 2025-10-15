<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Services\IPermissionService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        private readonly IPermissionService $permissionService
    ) {}

    /**
     * Listar permisos con filtros
     * GET /permissions
     */
    public function index(): JsonResponse
    {
        return $this->permissionService->getPermissions();
    }

    /**
     * Mostrar permiso específico
     * GET /permissions/{permission}
     */
    public function show(Permission $permission): JsonResponse
    {
        return $this->permissionService->getPermission($permission);
    }

    /**
     * Crear nuevo permiso
     * POST /permissions
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        return $this->permissionService->createPermission($request);
    }

    /**
     * Actualizar permiso
     * PUT /permissions/{permission}
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        return $this->permissionService->updatePermission($request, $permission);
    }

    /**
     * Eliminar permiso
     * DELETE /permissions/{permission}
     */
    public function destroy(Permission $permission): JsonResponse
    {
        return $this->permissionService->deletePermission($permission);
    }
}
