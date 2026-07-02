<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAuditRequest;
use App\Services\IAuditService;
use Illuminate\Http\JsonResponse;

class AuditController extends Controller
{
    public function __construct(
        private readonly IAuditService $auditService,
    ) {}

    public function index(IndexAuditRequest $request): JsonResponse
    {
        return $this->auditService->index($request);
    }
}
