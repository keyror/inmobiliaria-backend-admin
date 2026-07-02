<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\ITenantUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantUserController extends Controller
{
    public function __construct(
        private readonly ITenantUserService $tenantUserService
    ) {}

    public function index(Tenant $tenant): JsonResponse
    {
        return $this->tenantUserService->index($tenant);
    }

    public function show(Tenant $tenant, string $userId): JsonResponse
    {
        return $this->tenantUserService->show($tenant, $userId);
    }

    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        return $this->tenantUserService->store($request, $tenant);
    }

    public function update(Request $request, Tenant $tenant, string $userId): JsonResponse
    {
        return $this->tenantUserService->update($request, $tenant, $userId);
    }

    public function destroy(Tenant $tenant, string $userId): JsonResponse
    {
        return $this->tenantUserService->destroy($tenant, $userId);
    }

    public function roles(Tenant $tenant): JsonResponse
    {
        return $this->tenantUserService->roles($tenant);
    }

    public function statuses(Tenant $tenant): JsonResponse
    {
        return $this->tenantUserService->statuses($tenant);
    }
}
