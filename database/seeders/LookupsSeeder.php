<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Str;

class LookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = File::files(database_path('lookups'));

        foreach ($files as $file) {
            $lookups = require $file->getPathname();
            foreach ($lookups as $lookup) {
                DB::table('lookups')->insert([
                    'id' => Str::uuid(),
                    'category' => $lookup['category'],
                    'name' => $lookup['name'],
                    'alias' => $lookup['alias'],
                    'code' => $lookup['code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->seedColombiaDepartmentsAndCities();
    }

    private function makeAlias(string $value): string
    {
        $value = mb_strtoupper($value, 'UTF-8');
        $value = str_replace(
            ['Á','É','Í','Ó','Ú','Ñ'],
            ['A','E','I','O','U','N'],
            $value
        );

        return str_replace(' ', '_', $value);
    }

    private function seedColombiaDepartmentsAndCities(): void
    {
        $colombiaJson = File::get(database_path('json/colombia.json'));
        $colombia = json_decode($colombiaJson, true);

        foreach ($colombia as $item) {

            $departmentAlias = $this->makeAlias($item['departamento']);

            DB::table('lookups')->insert([
                'id' => Str::uuid(),
                'category' => 'department',
                'name' => $item['departamento'],
                'alias' => $departmentAlias,
                'code' => 'CO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($item['ciudades'] as $city) {
                DB::table('lookups')->insert([
                    'id' => Str::uuid(),
                    'category' => 'city',
                    'name' => $city,
                    'alias' => $this->makeAlias($city),
                    'code' => $departmentAlias,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }


}
