<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IDashboardService
{
    public function getStats(): JsonResponse;
}
