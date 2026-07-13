<?php

namespace Database\Seeders;

use App\Models\ContractClause;
use App\Support\ContractClauseDefaults;
use Illuminate\Database\Seeder;

class ContractClausesSeeder extends Seeder
{
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $defaults = app(ContractClauseDefaults::class);

        foreach ($defaults->getAvailableTemplates() as $templateKey => $info) {
            $clauses = $defaults->getDefaults($templateKey);

            if (! $clauses) {
                continue;
            }

            foreach ($clauses as $clause) {
                ContractClause::firstOrCreate(
                    [
                        'template_key' => $templateKey,
                        'section_key' => $clause['section_key'],
                    ],
                    array_merge($clause, [
                        'template_key' => $templateKey,
                        'is_active' => true,
                        'is_default' => true,
                    ])
                );
            }
        }

        $total = ContractClause::count();
        $this->command->info("✓ ContractClausesSeeder: {$total} cláusulas disponibles en la base de datos.");
    }
}
