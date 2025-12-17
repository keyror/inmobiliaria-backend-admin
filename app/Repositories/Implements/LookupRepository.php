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

    public function getColombiaWithDepartmentsAndCities(): ?Lookup
    {
        $columns = ['id', 'name', 'alias', 'code'];
        return Lookup::countries()
            ->select($columns)
            ->where('code', 'CO')
            ->with([
                'departments' => function ($q) use ($columns) {
                    $q->select($columns)
                        ->with([
                            'cities' => function ($q) use ($columns) {
                                $q->select($columns);
                            }
                        ]);
                }
            ])
            ->first();
    }



}
