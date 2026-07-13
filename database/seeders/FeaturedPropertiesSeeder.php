<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class FeaturedPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        Property::query()
            ->inRandomOrder()
            ->limit(5)
            ->update(['is_featured' => true]);
    }
}
