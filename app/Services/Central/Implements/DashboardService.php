<?php

namespace App\Services\Central\Implements;

use App\Http\Resources\CentralDashboardResource;
use App\Repositories\Central\IDashboardRepository;
use App\Services\Central\IDashboardService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardService implements IDashboardService
{
    public function __construct(
        private readonly IDashboardRepository $dashboardRepository,
    ) {}

    public function getStats(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new CentralDashboardResource($this->dashboardRepository->getStats()),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
