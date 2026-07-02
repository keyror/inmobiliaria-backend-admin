<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ITenantUserService
{
    public function index(Tenant $tenant): JsonResponse;

    public function show(Tenant $tenant, string $userId): JsonResponse;

    public function store(Request $request, Tenant $tenant): JsonResponse;

    public function update(Request $request, Tenant $tenant, string $userId): JsonResponse;

    public function destroy(Tenant $tenant, string $userId): JsonResponse;

    public function roles(Tenant $tenant): JsonResponse;

    public function statuses(Tenant $tenant): JsonResponse;
}
