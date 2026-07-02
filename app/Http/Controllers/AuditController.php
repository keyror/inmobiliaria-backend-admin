<?php

namespace App\Http\Controllers;

use App\Services\IAuditService;
use Illuminate\Http\JsonResponse;

class AuditController extends Controller
{
    public function __construct(
        private readonly IAuditService $auditService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->auditService->index();
    }
}
