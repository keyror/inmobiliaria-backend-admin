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

        // Central (SaaS admin) permissions only.
        // Tenant-specific permissions (properties, rents, people, etc.) live
        // in RolesAndPermissionsTenantSeeder.
        $permissions = [
            // Users — central admin management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',
            // Roles & permissions — central
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign-permissions',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            // Tenant management
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
            // Plans (SaaS pricing)
            'plans.view',
            'plans.create',
            'plans.edit',
            'plans.delete',
            // Lookups — global catalogue management
            'lookups.view',
            'lookups.create',
            'lookups.edit',
            'lookups.delete',
            // SaaS company profile
            'companies.view',
            'companies.edit',
            // Dashboard
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

        // Central only has two roles: Super Admin (all) and Admin (no tenant/plan management)
        $roles = ['Super Admin', 'Admin'];

        Role::query()
            ->where('guard_name', 'api')
            ->whereNotIn('name', $roles)
            ->get()
            ->each
            ->delete();

        $superAdminRole = Role::findOrCreate('Super Admin', 'api');
        $adminRole = Role::findOrCreate('Admin', 'api');

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

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
