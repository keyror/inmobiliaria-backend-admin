<?php

namespace App\Services\Implements;

use App\Http\Requests\IndexAuditRequest;
use App\Http\Resources\AuditResource;
use App\Repositories\IAuditRepository;
use App\Services\IAuditService;
use Exception;
use Illuminate\Http\JsonResponse;

class AuditService implements IAuditService
{
    public function __construct(
        private readonly IAuditRepository $auditRepository,
    ) {}

    public function index(IndexAuditRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $paginator = $this->auditRepository->getAuditLogs($filters);

            return response()->json([
                'status' => true,
                'data' => AuditResource::collection($paginator->items()),
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
