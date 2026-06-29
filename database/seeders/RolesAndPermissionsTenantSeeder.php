<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign-permissions',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'lookups.view',
            'lookups.create',
            'lookups.edit',
            'lookups.delete',
            'companies.view',
            'companies.create',
            'companies.edit',
            'site-settings.theme-view',
            'site-settings.view',
            'site-settings.edit',
            'fiscal-profiles.create',
            'fiscal-profiles.edit',
            'fiscal-profiles.delete',
            'people.view',
            'people.create',
            'people.edit',
            'people.delete',
            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.delete',
            'images.create',
            'images.edit',
            'images.delete',
            'dashboard.view',
        ];

        Permission::query()
            ->where('guard_name', 'api')
            ->whereNotIn('name', $permissions)
            ->get()
            ->each
            ->delete();

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        $roles = ['Admin', 'Agent'];

        Role::query()
            ->where('guard_name', 'api')
            ->whereNotIn('name', $roles)
            ->get()
            ->each
            ->delete();

        $adminRole = Role::findOrCreate('Admin', 'api');
        $agentRole = Role::findOrCreate('Agent', 'api');

        $allPermissions = Permission::query()
            ->where('guard_name', 'api')
            ->get();

        $adminRole->syncPermissions($allPermissions);

        $agentExcluded = [
            'companies.view',
            'companies.create',
            'companies.edit',
            'site-settings.view',
            'site-settings.edit',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign-permissions',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'lookups.view',
            'lookups.create',
            'lookups.edit',
            'lookups.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',
        ];

        $agentRole->syncPermissions($allPermissions->whereNotIn('name', $agentExcluded)->values());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
