<?php

namespace App\Repositories;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

interface IRoleRepository
{
    public function getRolesByFilters(): LengthAwarePaginator;
    public function create(StoreRoleRequest $request): void;
    public function update(UpdateRoleRequest $request, Role $role): void;
    public function delete(Role $role): void;
    public function assignPermissions(AssignPermissionsRequest $request, Role $role): void;
}
