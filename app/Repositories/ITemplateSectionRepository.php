<?php

namespace App\Repositories;

use App\Models\TemplateSection;
use Illuminate\Database\Eloquent\Collection;

interface ITemplateSectionRepository
{
    public function getByTemplate(string $templateKey): Collection;

    public function find(int $id): ?TemplateSection;

    public function create(array $data): TemplateSection;

    public function update(TemplateSection $clause, array $data): TemplateSection;

    public function delete(TemplateSection $clause): void;

    public function reorder(string $templateKey, array $orderedIds): void;

    public function seedDefaults(string $templateKey, array $clauses): void;
}
