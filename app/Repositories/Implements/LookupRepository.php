<?php

namespace App\Repositories\Implements;

use App\Models\Lookup;
use App\Repositories\ILookupRepository;
use Illuminate\Support\Collection;

class LookupRepository implements ILookupRepository
{
    public function getLookupsByCategory(array $categories): Collection
    {
        return Lookup::query()
            ->whereIn('category', $categories)
            ->orderBy('name')
            ->get()
            ->groupBy('category')
            ->map(function ($items) {
                return $items->values();
            });
    }
}
