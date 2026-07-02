<?php

namespace App\Services;

use App\Http\Requests\IndexAuditRequest;
use Illuminate\Http\JsonResponse;

interface IAuditService
{
    public function index(IndexAuditRequest $request): JsonResponse;
}
