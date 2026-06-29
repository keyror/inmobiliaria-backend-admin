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
            $resource = new DashboardResource([
                'stats' => $this->dashboardRepository->getStats(),
                'recent_properties' => $this->dashboardRepository->getRecentProperties(),
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
}
