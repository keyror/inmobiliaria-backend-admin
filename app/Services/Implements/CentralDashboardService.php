<?php

namespace App\Services\Implements;

use App\Http\Resources\CentralDashboardResource;
use App\Repositories\ICentralDashboardRepository;
use App\Services\ICentralDashboardService;
use Exception;
use Illuminate\Http\JsonResponse;

class CentralDashboardService implements ICentralDashboardService
{
    public function __construct(
        private readonly ICentralDashboardRepository $dashboardRepository,
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
