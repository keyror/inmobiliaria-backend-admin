<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Repositories\IPermissionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements IPermissionRepository
{
    public function getPermissionsByFilters(): LengthAwarePaginator
    {
        $permissions = Permission::query()
            ->with('roles')
            ->allowedFilters(['name', 'guard_name', 'created_at'])
            ->allowedSorts(['name', 'guard_name', 'created_at'])
            ->jsonPaginate();

        return $permissions;
    }

    public function create(StorePermissionRequest $request): void
    {
        Permission::create([
            'name' => $request->name,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): void
    {
        $permission->update([
            'name' => $request->name,
        ]);
    }

    public function delete(Permission $permission): void
    {
        // Revocar el permiso de todos los roles antes de eliminar
        foreach ($permission->roles as $role) {
            $role->revokePermissionTo($permission);
        }

        // Eliminar el permiso
        $permission->delete();
    }
}
