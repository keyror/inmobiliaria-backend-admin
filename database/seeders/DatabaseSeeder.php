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
        $this->call(RealstateSiteSettingsSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PersonsTableSeeder::class);
        $this->call(PropertiesTableSeeder::class);
        $this->call(FeaturedPropertiesSeeder::class);
        $this->call(DocumentTemplatesSeeder::class);
        $this->call(TemplateSectionsSeeder::class);
        $this->call(ReportTemplateSeeder::class);
    }
}
