<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\Central\ITenantService;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function __construct(
        private readonly ITenantService $tenantService
    ) {}

    public function index(): JsonResponse
    {
        return $this->tenantService->getTenants();
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->getTenant($tenant);
    }

    public function store(StoreTenantRequest $request): JsonResponse
    {
        return $this->tenantService->createTenant($request);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        return $this->tenantService->updateTenant($request, $tenant);
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->deleteTenant($tenant);
    }

    public function activate(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->activateTenant($tenant);
    }

    public function deactivate(Tenant $tenant): JsonResponse
    {
        return $this->tenantService->deactivateTenant($tenant);
    }
}
