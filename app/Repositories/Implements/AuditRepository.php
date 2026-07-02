<?php

namespace App\Repositories\Implements;

use App\Http\Resources\AuditResource;
use App\Models\AuditLog;
use App\Models\User;
use App\Repositories\IAuditRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRepository implements IAuditRepository
{
    public function getAuditLogs(): LengthAwarePaginator
    {
        return AuditLog::query()
            ->with('causer:id,email')
            ->when(request()->query('log_name'), fn ($q, $v) => $q->where('log_name', $v))
            ->when(request()->query('event'), fn ($q, $v) => $q->where('event', $v))
            ->when(request()->query('causer_email'), function ($q, $v) {
                $ids = User::where('email', 'LIKE', "%{$v}%")->pluck('id');
                $q->whereIn('causer_id', $ids);
            })
            ->when(request()->query('date_from'), fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request()->query('date_to'), fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->allowedSorts()
            ->jsonPaginate();
    }

    public function getLogsByBatch(string $batchUuid): array
    {
        return AuditLog::query()
            ->with('causer:id,email')
            ->where('batch_uuid', $batchUuid)
            ->oldest()
            ->get()
            ->map(fn ($log) => new AuditResource($log))
            ->values()
            ->all();
    }

    public function searchAuditLogs(string $term, int $limit = 5): array
    {
        $causerIds = User::where('email', 'LIKE', "%{$term}%")->pluck('id');

        return AuditLog::query()
            ->with('causer:id,email')
            ->where(function ($q) use ($term, $causerIds) {
                $q->where('log_name', 'LIKE', "%{$term}%")
                    ->orWhere('event', 'LIKE', "%{$term}%")
                    ->orWhereIn('causer_id', $causerIds);
            })
            ->latest()
            ->limit($limit)
            ->get(['id', 'log_name', 'event', 'causer_id', 'causer_type', 'created_at'])
            ->map(fn ($log) => [
                'id' => $log->id,
                'label' => collect([
                    __('audit.modules.'.$log->log_name, [], 'es'),
                    $log->event,
                ])->filter()->implode(' • '),
                'subtitle' => collect([
                    $log->causer?->email,
                    $log->created_at?->format('Y-m-d H:i'),
                ])->filter()->implode(' · '),
            ])
            ->values()
            ->all();
    }
}
