<?php

namespace Database\Seeders;

use App\Models\TemplateSection;
use App\Support\TemplateSectionDefaults;
use Illuminate\Database\Seeder;

class TemplateSectionsSeeder extends Seeder
{
    public function run(): void
    {
        config(['activitylog.enabled' => false]);

        $defaults = app(TemplateSectionDefaults::class);

        foreach ($defaults->getAvailableTemplates() as $templateKey => $info) {
            $clauses = $defaults->getDefaults($templateKey);

            if (! $clauses) {
                continue;
            }

            foreach ($clauses as $clause) {
                TemplateSection::firstOrCreate(
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

        $total = TemplateSection::count();
        $this->command->info("✓ TemplateSectionsSeeder: {$total} secciones disponibles en la base de datos.");
    }
}
