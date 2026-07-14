<?php

namespace App\Repositories\Implements;

use App\Models\ReportTemplate;
use App\Repositories\IReportTemplateRepository;
use Illuminate\Support\Collection;

class ReportTemplateRepository implements IReportTemplateRepository
{
    public function all(): Collection
    {
        return ReportTemplate::orderByDesc('is_default')->orderBy('name')->get();
    }

    public function find(string $id): ReportTemplate
    {
        return ReportTemplate::findOrFail($id);
    }

    public function getDefault(): ?ReportTemplate
    {
        return ReportTemplate::where('is_default', true)->first()
            ?? ReportTemplate::first();
    }

    public function create(array $data): ReportTemplate
    {
        return ReportTemplate::create($data);
    }

    public function update(ReportTemplate $template, array $data): ReportTemplate
    {
        $template->update($data);

        return $template->fresh();
    }

    public function delete(ReportTemplate $template): void
    {
        $template->delete();
    }
}
