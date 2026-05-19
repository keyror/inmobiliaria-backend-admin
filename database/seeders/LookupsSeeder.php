<?php

namespace Database\Seeders;

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
                $lookupQuery = DB::table('lookups')
                    ->where('category', $lookup['category'])
                    ->where('alias', $lookup['alias']);

                if ($lookupQuery->exists()) {
                    $lookupQuery->update([
                        'name' => $lookup['name'],
                        'code' => $lookup['code'],
                        'icon' => $lookup['icon'] ?? null,
                        'updated_at' => now(),
                    ]);

                    continue;
                }

                DB::table('lookups')->insert([
                    'id' => Str::uuid(),
                    'category' => $lookup['category'],
                    'name' => $lookup['name'],
                    'alias' => $lookup['alias'],
                    'code' => $lookup['code'],
                    'icon' => $lookup['icon'] ?? null,
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
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['A', 'E', 'I', 'O', 'U', 'N'],
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

            $departmentQuery = DB::table('lookups')
                ->where('category', 'department')
                ->where('alias', $departmentAlias);

            if ($departmentQuery->exists()) {
                $departmentQuery->update([
                    'name' => $item['departamento'],
                    'code' => 'CO',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('lookups')->insert([
                    'id' => Str::uuid(),
                    'category' => 'department',
                    'name' => $item['departamento'],
                    'alias' => $departmentAlias,
                    'code' => 'CO',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($item['ciudades'] as $city) {
                $cityAlias = $this->makeAlias($city);
                $cityQuery = DB::table('lookups')
                    ->where('category', 'city')
                    ->where('alias', $cityAlias)
                    ->where('code', $departmentAlias);

                if ($cityQuery->exists()) {
                    $cityQuery->update([
                        'name' => $city,
                        'updated_at' => now(),
                    ]);

                    continue;
                }

                DB::table('lookups')->insert([
                    'id' => Str::uuid(),
                    'category' => 'city',
                    'name' => $city,
                    'alias' => $cityAlias,
                    'code' => $departmentAlias,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
