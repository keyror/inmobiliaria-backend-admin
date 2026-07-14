<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
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
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',
            'tenants.activate',
            'tenants.deactivate',
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
            'plans.view',
            'plans.create',
            'plans.edit',
            'plans.delete',
            'rents.view',
            'rents.create',
            'rents.edit',
            'rents.delete',
            'documents.view',
            'documents.create',
            'documents.generate',
            'documents.sign',
            'documents.archive',
            'documents.delete',
            'documents.export',
            'audit.view',
            'audit.export',
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',
            'reports.export',
            'tenants.users.view',
            'tenants.users.create',
            'tenants.users.edit',
            'tenants.users.delete',
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

        $roles = ['Super Admin', 'Admin', 'Agent'];

        Role::query()
            ->where('guard_name', 'api')
            ->whereNotIn('name', $roles)
            ->get()
            ->each
            ->delete();

        $superAdminRole = Role::findOrCreate('Super Admin', 'api');
        $adminRole = Role::findOrCreate('Admin', 'api');
        $agentRole = Role::findOrCreate('Agent', 'api');

        $allPermissions = Permission::query()
            ->where('guard_name', 'api')
            ->get();

        $superAdminRole->syncPermissions($allPermissions);

        $adminExcluded = [
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',
            'tenants.activate',
            'tenants.deactivate',
            'tenants.users.view',
            'tenants.users.create',
            'tenants.users.edit',
            'tenants.users.delete',
            'plans.view',
            'plans.create',
            'plans.edit',
            'plans.delete',
        ];

        $adminRole->syncPermissions($allPermissions->whereNotIn('name', $adminExcluded)->values());

        $agentExcluded = [
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',
            'tenants.activate',
            'tenants.deactivate',
            'tenants.users.view',
            'tenants.users.create',
            'tenants.users.edit',
            'tenants.users.delete',
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
            'plans.view',
            'plans.create',
            'plans.edit',
            'plans.delete',
            'audit.view',
            'audit.export',
            'reports.create',
            'reports.edit',
            'reports.delete',
        ];

        $agentRole->syncPermissions($allPermissions->whereNotIn('name', $agentExcluded)->values());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
