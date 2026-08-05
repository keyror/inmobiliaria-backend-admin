<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Services\Central\IDashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly IDashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->dashboardService->getStats();
    }
}
