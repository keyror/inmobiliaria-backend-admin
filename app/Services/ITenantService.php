<?php

namespace App\Services;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

interface ITenantService
{
    public function getTenants(): JsonResponse;
    public function getTenant(Tenant $tenant): JsonResponse;
    public function createTenant(StoreTenantRequest $request): JsonResponse;
    public function updateTenant(UpdateTenantRequest $request, Tenant $tenant): JsonResponse;
    public function deleteTenant(Tenant $tenant): JsonResponse;
    public function activateTenant(Tenant $tenant): JsonResponse;
    public function deactivateTenant(Tenant $tenant): JsonResponse;
}
