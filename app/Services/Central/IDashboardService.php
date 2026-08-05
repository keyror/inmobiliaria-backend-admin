<?php

namespace App\Services\Central;

use Illuminate\Http\JsonResponse;

interface IDashboardService
{
    public function getStats(): JsonResponse;
}
