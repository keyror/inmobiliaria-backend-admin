<?php

namespace Database\Seeders;

use App\Models\Lookup;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $mensualId = Lookup::where('category', 'frequency')->where('alias', 'MENSUAL')->value('id');
        $anualId = Lookup::where('category', 'frequency')->where('alias', 'ANUAL')->value('id');

        $plans = [
            // Planes mensuales
            [
                'name' => 'Básico',
                'description' => 'Ideal para inmobiliarias pequeñas que están comenzando.',
                'price' => 150000,
                'frequency_type_id' => $mensualId,
                'discount' => null,
                'max_users' => 3,
                'max_properties' => 30,
                'max_images_per_property' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Profesional',
                'description' => 'Para inmobiliarias en crecimiento con mayor volumen.',
                'price' => 200000,
                'frequency_type_id' => $mensualId,
                'discount' => null,
                'max_users' => 10,
                'max_properties' => 150,
                'max_images_per_property' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Empresarial',
                'description' => 'Sin límites prácticos para grandes inmobiliarias.',
                'price' => 500000,
                'frequency_type_id' => $mensualId,
                'discount' => null,
                'max_users' => 30,
                'max_properties' => 1000,
                'max_images_per_property' => 30,
                'is_active' => true,
            ],
            // Planes anuales
            [
                'name' => 'Básico Anual',
                'description' => 'Plan Básico con facturación anual y descuento incluido.',
                'price' => 150000,
                'frequency_type_id' => $anualId,
                'discount' => 5,
                'max_users' => 3,
                'max_properties' => 30,
                'max_images_per_property' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Profesional Anual',
                'description' => 'Plan Profesional con facturación anual y descuento incluido.',
                'price' => 200000,
                'frequency_type_id' => $anualId,
                'discount' => 7,
                'max_users' => 10,
                'max_properties' => 150,
                'max_images_per_property' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Empresarial Anual',
                'description' => 'Plan Empresarial con facturación anual y descuento incluido.',
                'price' => 500000,
                'frequency_type_id' => $anualId,
                'discount' => 10,
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
