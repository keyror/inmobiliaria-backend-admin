<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(LookupsSeeder::class);
        $this->call(PlansSeeder::class);
        $this->call(CentralCompanySeeder::class);
        $this->call(CentralSiteSettingSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
