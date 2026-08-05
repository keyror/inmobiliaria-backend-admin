<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class FeaturedPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        Property::whereIn('code', ['PROP-000001', 'PROP-000002', 'PROP-000003'])
            ->update(['is_featured' => true]);
    }
}
