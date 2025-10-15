<?php

namespace App\Repositories\Implements;

use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\IRoleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository implements IRoleRepository
{
    public function getRolesByFilters(): LengthAwarePaginator
    {
        $roles = Role::query()
            ->with('permissions')
            ->allowedFilters(['name', 'guard_name', 'created_at'])
            ->allowedSorts(['name', 'guard_name', 'created_at'])
            ->jsonPaginate();

        return $roles;
    }

    public function create(StoreRoleRequest $request): void
    {
        Role::create([
            'name' => $request->name,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): void
    {
        $role->update([
            'name' => $request->name,
        ]);
    }

    public function delete(Role $role): void
    {
        // Revocar todos los permisos antes de eliminar
        $role->revokePermissionTo($role->permissions);

        // Eliminar el rol
        $role->delete();
    }

    public function assignPermissions(AssignPermissionsRequest $request, Role $role): void
    {
        // Obtener los permisos por IDs
        $permissions = Permission::whereIn('id', $request->permissions)->get();

        // Sincronizar permisos (elimina los antiguos y asigna los nuevos)
        $role->syncPermissions($permissions);
    }
}
