<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsTenantSeeder::class);
        $this->call(LookupsSeeder::class);
        $this->call(RealstateSiteSettingsSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(TenantUsersTableSeeder::class);
    }
}
