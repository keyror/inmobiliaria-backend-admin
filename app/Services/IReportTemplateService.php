<?php

namespace App\Services;

use App\Http\Requests\StoreReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use App\Models\ReportTemplate;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface IReportTemplateService
{
    public function getTemplates(): JsonResponse;

    public function getTemplate(ReportTemplate $template): JsonResponse;

    public function getVariables(): JsonResponse;

    public function createTemplate(StoreReportTemplateRequest $request): JsonResponse;

    public function updateTemplate(UpdateReportTemplateRequest $request, ReportTemplate $template): JsonResponse;

    public function deleteTemplate(ReportTemplate $template): JsonResponse;

    public function preview(ReportTemplate $template, array $filters, int $page, int $perPage): JsonResponse;

    public function export(ReportTemplate $template, array $filters): BinaryFileResponse;
}
