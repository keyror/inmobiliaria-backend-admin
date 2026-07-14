<?php

namespace App\Services\Implements;

use App\Exports\excel\RentReportExport;
use App\Http\Requests\StoreReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use App\Models\Rent;
use App\Models\ReportTemplate;
use App\Repositories\IReportTemplateRepository;
use App\Services\IReportTemplateService;
use App\Support\ReportVariables;
use Exception;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportTemplateService implements IReportTemplateService
{
    public function __construct(
        private readonly IReportTemplateRepository $repository
    ) {}

    public function getTemplates(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->repository->all(),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getTemplate(ReportTemplate $template): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $template,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getVariables(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => ReportVariables::catalog(),
        ]);
    }

    public function createTemplate(StoreReportTemplateRequest $request): JsonResponse
    {
        try {
            $template = $this->repository->create($request->validated());

            return response()->json(['status' => true, 'data' => $template], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateTemplate(UpdateReportTemplateRequest $request, ReportTemplate $template): JsonResponse
    {
        try {
            $updated = $this->repository->update($template, $request->validated());

            return response()->json(['status' => true, 'data' => $updated]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function deleteTemplate(ReportTemplate $template): JsonResponse
    {
        try {
            if ($template->is_default) {
                return response()->json(['status' => false, 'message' => 'No se puede eliminar la plantilla predeterminada.'], 422);
            }
            $this->repository->delete($template);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function preview(ReportTemplate $template, array $filters, int $page, int $perPage): JsonResponse
    {
        try {
            $columns = $template->columns;
            $keys = array_column($columns, 'key');
            $loads = ReportVariables::requiredLoads($keys);

            $query = Rent::query()->with($loads);
            $query = $this->applyFilters($query, $filters);

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            $rows = collect($paginated->items())->map(function (Rent $rent) use ($columns) {
                $row = [];
                foreach ($columns as $col) {
                    $row[$col['key']] = ReportVariables::resolve($rent, $col['key']);
                }

                return $row;
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'rows' => $rows,
                    'columns' => $columns,
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function export(ReportTemplate $template, array $filters): BinaryFileResponse
    {
        $columns = $template->columns;
        $keys = array_column($columns, 'key');
        $loads = ReportVariables::requiredLoads($keys);

        $query = Rent::query()->with($loads);
        $query = $this->applyFilters($query, $filters);
        $rents = $query->get();

        $filename = str($template->name)->slug('-').'-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new RentReportExport($rents, $columns), $filename);
    }

    private function applyFilters($query, array $filters)
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['contract_type_id'])) {
            $query->where('contract_type_id', $filters['contract_type_id']);
        }
        if (! empty($filters['start_from'])) {
            $query->where('start_date', '>=', $filters['start_from']);
        }
        if (! empty($filters['start_to'])) {
            $query->where('start_date', '<=', $filters['start_to']);
        }

        return $query;
    }
}
