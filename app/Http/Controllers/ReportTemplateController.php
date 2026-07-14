<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use App\Models\ReportTemplate;
use App\Services\IReportTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportTemplateController extends Controller
{
    public function __construct(
        private readonly IReportTemplateService $service
    ) {}

    public function index(): JsonResponse
    {
        return $this->service->getTemplates();
    }

    public function show(ReportTemplate $reportTemplate): JsonResponse
    {
        return $this->service->getTemplate($reportTemplate);
    }

    public function variables(): JsonResponse
    {
        return $this->service->getVariables();
    }

    public function store(StoreReportTemplateRequest $request): JsonResponse
    {
        return $this->service->createTemplate($request);
    }

    public function update(UpdateReportTemplateRequest $request, ReportTemplate $reportTemplate): JsonResponse
    {
        return $this->service->updateTemplate($request, $reportTemplate);
    }

    public function destroy(ReportTemplate $reportTemplate): JsonResponse
    {
        return $this->service->deleteTemplate($reportTemplate);
    }

    public function preview(Request $request, ReportTemplate $reportTemplate): JsonResponse
    {
        return $this->service->preview(
            $reportTemplate,
            $request->only(['status', 'contract_type_id', 'start_from', 'start_to']),
            (int) $request->query('page', 1),
            (int) $request->query('per_page', 50)
        );
    }

    public function export(Request $request, ReportTemplate $reportTemplate): BinaryFileResponse
    {
        return $this->service->export(
            $reportTemplate,
            $request->only(['status', 'contract_type_id', 'start_from', 'start_to'])
        );
    }
}
