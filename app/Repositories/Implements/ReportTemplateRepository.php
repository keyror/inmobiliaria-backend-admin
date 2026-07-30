<?php

namespace App\Repositories\Implements;

use App\Models\ReportTemplate;
use App\Repositories\IReportTemplateRepository;
use Illuminate\Support\Collection;

class ReportTemplateRepository implements IReportTemplateRepository
{
    public function all(?string $companyId = null): Collection
    {
        return ReportTemplate::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    public function find(string $id): ReportTemplate
    {
        return ReportTemplate::findOrFail($id);
    }

    public function getDefault(?string $companyId = null): ?ReportTemplate
    {
        $query = ReportTemplate::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        return (clone $query)->where('is_default', true)->first()
            ?? $query->first();
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
