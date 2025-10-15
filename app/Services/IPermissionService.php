<?php

namespace App\Services;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

interface IPermissionService
{
    public function getPermissions(): JsonResponse;
    public function createPermission(StorePermissionRequest $request): JsonResponse;
    public function updatePermission(UpdatePermissionRequest $request, Permission $permission): JsonResponse;
    public function deletePermission(Permission $permission): JsonResponse;
}
