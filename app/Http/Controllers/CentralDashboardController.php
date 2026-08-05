<?php

namespace App\Http\Controllers;

use App\Services\ICentralDashboardService;
use Illuminate\Http\JsonResponse;

class CentralDashboardController extends Controller
{
    public function __construct(
        private readonly ICentralDashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->dashboardService->getStats();
    }
}
