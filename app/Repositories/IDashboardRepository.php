<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface IDashboardRepository
{
    /**
     * @return array{
     *     total_properties: int,
     *     total_people: int,
     *     featured_properties: int,
     *     properties_this_month: int,
     *     by_offer_type: array<int, array{name: string, total: int}>,
     *     by_status: array<int, array{name: string, total: int}>
     * }
     */
    public function getStats(): array;

    public function getRecentProperties(int $limit = 5): Collection;
}
