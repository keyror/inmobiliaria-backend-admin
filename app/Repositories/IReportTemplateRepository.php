<?php

namespace App\Repositories;

use App\Models\ReportTemplate;
use Illuminate\Support\Collection;

interface IReportTemplateRepository
{
    public function all(?string $companyId = null): Collection;

    public function find(string $id): ReportTemplate;

    public function getDefault(?string $companyId = null): ?ReportTemplate;

    public function create(array $data): ReportTemplate;

    public function update(ReportTemplate $template, array $data): ReportTemplate;

    public function delete(ReportTemplate $template): void;
}
