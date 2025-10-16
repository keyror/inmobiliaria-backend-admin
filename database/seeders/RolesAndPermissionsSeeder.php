<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear Permisos
        $permissions = [
            // Permisos de Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',

            // Permisos de Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign-permissions',

            // Permisos de Permisos
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Permisos de Tenants
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',
            'tenants.activate',
            'tenants.deactivate',

            // Permisos adicionales
            'dashboard.view',
            'reports.view',
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'api']
            );
        }

        // Crear Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['guard_name' => 'api']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['guard_name' => 'api']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'User'],
            ['guard_name' => 'api']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'api']
        );

        // Asignar todos los permisos a Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Asignar permisos específicos a Admin
        $adminPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.export',
            'roles.view',
            'permissions.view',
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'dashboard.view',
            'reports.view',
            'settings.view',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Asignar permisos específicos a Manager
        $managerPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'tenants.view',
            'dashboard.view',
            'reports.view',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Asignar permisos básicos a User
        $userPermissions = [
            'dashboard.view',
            'users.view',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
