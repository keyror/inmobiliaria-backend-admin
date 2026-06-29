<?php

namespace App\Http\Controllers;

use App\Services\IDashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly IDashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        return $this->dashboardService->getStats();
    }
}
