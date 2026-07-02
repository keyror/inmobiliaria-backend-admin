<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface IAuditRepository
{
    public function getAuditLogs(): LengthAwarePaginator;

    public function searchAuditLogs(string $term, int $limit = 5): array;
}
