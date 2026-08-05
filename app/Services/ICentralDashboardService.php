<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface ICentralDashboardService
{
    public function getStats(): JsonResponse;
}
