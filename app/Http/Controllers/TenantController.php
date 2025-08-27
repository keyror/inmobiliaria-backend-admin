<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\ITenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function __construct(
        private readonly ITenantService $tenantService
    ) {}

    /**
     * Listar tenants con filtros
     */
    public function index(): JsonResponse
    {
        return $this->tenantService->getTenants();
    }

    /**
     * Mostrar tenant específico
     */
    public function show(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->getTenant($tenant);
    }

    /**
     * Crear nuevo tenant
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        return $this->tenantService->createTenant($request);
    }

    /**
     * Actualizar tenant
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        return $this->tenantService->updateTenant($request, $tenant);
    }

    /**
     * Eliminar tenant
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->deleteTenant($tenant);
    }

    /**
     * Activar tenant
     */
    public function activate(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->activateTenant($tenant);
    }

    /**
     * Desactivar tenant
     */
    public function deactivate(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->deactivateTenant($tenant);
    }
}
