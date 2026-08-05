<?php

namespace Database\Seeders;

use App\Models\CentralSiteSetting;
use Illuminate\Database\Seeder;

class CentralSiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        CentralSiteSetting::query()->firstOrCreate(
            [],
            [
                'template_set' => null,
                'theme' => [
                    'primary' => '#46b5d1',
                    'secondary' => '#1f2937',
                    'accent' => '#f35d43',
                ],
                'pages' => null,
            ],
        );
    }
}
