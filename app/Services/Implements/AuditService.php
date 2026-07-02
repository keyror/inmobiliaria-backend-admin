<?php

namespace App\Services\Implements;

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

    public function index(): JsonResponse
    {
        try {
            $paginator = $this->auditRepository->getAuditLogs();

            return response()->json([
                'status' => true,
                'data' => $paginator->through(fn ($log) => new AuditResource($log)),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function getBatch(string $batchUuid): JsonResponse
    {
        try {
            $logs = $this->auditRepository->getLogsByBatch($batchUuid);

            return response()->json([
                'status' => true,
                'data' => $logs,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
