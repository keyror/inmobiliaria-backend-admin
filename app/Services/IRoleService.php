<?php

namespace App\Services;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

interface IRoleService
{
    public function getRoles(): JsonResponse;
    public function createRole(StoreRoleRequest $request): JsonResponse;
    public function updateRole(UpdateRoleRequest $request, Role $role): JsonResponse;
    public function deleteRole(Role $role): JsonResponse;
    public function assignPermissions(AssignPermissionsRequest $request, Role $role): JsonResponse;
}
