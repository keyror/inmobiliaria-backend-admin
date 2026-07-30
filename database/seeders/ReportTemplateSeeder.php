<?php

namespace Database\Seeders;

use App\Models\Company;
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

        $hq = Company::whereNull('parent_company_id')->first();

        ReportTemplate::create([
            'company_id' => $hq?->id,
            'name' => 'Contratos',
            'columns' => ReportVariables::defaultColumns(),
            'is_default' => true,
        ]);
    }
}
