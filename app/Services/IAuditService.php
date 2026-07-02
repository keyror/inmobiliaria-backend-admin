<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IAuditService
{
    public function index(): JsonResponse;

    public function getBatch(string $batchUuid): JsonResponse;
}
