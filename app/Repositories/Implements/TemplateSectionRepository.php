<?php

namespace App\Repositories\Implements;

use App\Models\TemplateSection;
use App\Repositories\ITemplateSectionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TemplateSectionRepository implements ITemplateSectionRepository
{
    public function getByTemplate(string $templateKey): Collection
    {
        return TemplateSection::forTemplate($templateKey)->get();
    }

    public function find(int $id): ?TemplateSection
    {
        return TemplateSection::find($id);
    }

    public function create(array $data): TemplateSection
    {
        return TemplateSection::create($data);
    }

    public function update(TemplateSection $clause, array $data): TemplateSection
    {
        $clause->update($data);

        return $clause->fresh();
    }

    public function delete(TemplateSection $clause): void
    {
        $clause->delete();
    }

    public function reorder(string $templateKey, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                TemplateSection::where('id', $id)->update(['sort_order' => $order]);
            }
        });
    }

    public function seedDefaults(string $templateKey, array $clauses): void
    {
        foreach ($clauses as $clause) {
            TemplateSection::firstOrCreate(
                ['template_key' => $templateKey, 'section_key' => $clause['section_key']],
                array_merge($clause, ['is_default' => true])
            );
        }
    }
}
