<?php

namespace Database\Seeders;

use App\Models\ReportTemplate;
use App\Support\ReportVariables;
use Illuminate\Database\Seeder;

class ReportTemplateSeeder extends Seeder
{
    public function run(): void
    {
        if (ReportTemplate::exists()) {
            return;
        }

        ReportTemplate::create([
            'name' => 'Contratos',
            'columns' => ReportVariables::defaultColumns(),
            'is_default' => true,
        ]);
    }
}
