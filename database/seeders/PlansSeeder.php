<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'description' => 'Ideal para inmobiliarias pequeñas que están comenzando.',
                'price' => 150000,
                'max_users' => 3,
                'max_properties' => 30,
                'max_images_per_property' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Profesional',
                'description' => 'Para inmobiliarias en crecimiento con mayor volumen.',
                'price' => 200000,
                'max_users' => 10,
                'max_properties' => 150,
                'max_images_per_property' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Empresarial',
                'description' => 'Sin límites prácticos para grandes inmobiliarias.',
                'price' => 500000,
                'max_users' => 30,
                'max_properties' => 1000,
                'max_images_per_property' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $data) {
            Plan::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
