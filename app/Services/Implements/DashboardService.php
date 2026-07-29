<?php

namespace App\Services\Implements;

use App\Http\Resources\DashboardResource;
use App\Repositories\IDashboardRepository;
use App\Services\IDashboardService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardService implements IDashboardService
{
    public function __construct(
        private readonly IDashboardRepository $dashboardRepository
    ) {}

    public function getStats(): JsonResponse
    {
        try {
            $companyId = $this->resolveCompanyScope();

            $resource = new DashboardResource([
                'stats' => $this->dashboardRepository->getStats($companyId),
                'recent_properties' => $this->dashboardRepository->getRecentProperties(companyId: $companyId),
            ]);

            return response()->json([
                'status' => true,
                'data' => $resource->toArray(request()),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function resolveCompanyScope(): ?string
    {
        if (! request()->attributes->get('branch_scoping_active', false)) {
            return null;
        }

        if (request()->user()?->can('companies.view_all')) {
            return null;
        }

        return request()->attributes->get('current_company_id');
    }
}
