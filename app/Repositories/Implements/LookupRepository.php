<?php

namespace App\Repositories\Implements;

use App\Models\Lookup;
use App\Repositories\ILookupRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LookupRepository implements ILookupRepository
{
    public function getLookupsByFilters(): LengthAwarePaginator
    {
        return Lookup::query()
            ->when(request()->query('category'), function ($query, string $category) {
                $query->where('category', $category);
            })
            ->when(request()->query('is_active') !== null, function ($query) {
                $query->where('is_active', request()->boolean('is_active'));
            })
            ->when(request()->query('lang'), function ($query, string $lang) {
                $query->where('lang', $lang);
            })
            ->allowedFilters([
                'category',
                'name',
                'alias',
                'code',
                'lang',
                'icon',
            ])
            ->allowedSorts([
                'category',
                'name',
                'alias',
                'code',
                'value',
                'is_active',
                'lang',
                'created_at',
            ])
            ->jsonPaginate();
    }

    public function getLookupsByCategory(array $categories): Collection
    {
        return Lookup::query()
            ->whereIn('category', $categories)
            ->where('is_active', true)
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
                'departments' => function ($query) use ($columns) {
                    $query->select($columns)
                        ->with([
                            'cities' => function ($query) use ($columns) {
                                $query->select($columns);
                            },
                        ]);
                },
            ])
            ->first();
    }

    public function getCategories(): Collection
    {
        return Lookup::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();
    }

    public function create(array $data): Lookup
    {
        return Lookup::create([
            'category' => $data['category'],
            'name' => $data['name'],
            'alias' => $data['alias'] ?? null,
            'value' => $data['value'] ?? null,
            'code' => $data['code'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'lang' => $data['lang'] ?? 'ES',
            'icon' => $data['icon'] ?? null,
        ]);
    }

    public function update(Lookup $lookup, array $data): Lookup
    {
        $lookup->update([
            'category' => $data['category'] ?? $lookup->category,
            'name' => $data['name'] ?? $lookup->name,
            'alias' => array_key_exists('alias', $data) ? $data['alias'] : $lookup->alias,
            'value' => array_key_exists('value', $data) ? $data['value'] : $lookup->value,
            'code' => array_key_exists('code', $data) ? $data['code'] : $lookup->code,
            'is_active' => array_key_exists('is_active', $data) ? $data['is_active'] : $lookup->is_active,
            'lang' => $data['lang'] ?? $lookup->lang,
            'icon' => array_key_exists('icon', $data) ? $data['icon'] : $lookup->icon,
        ]);

        return $lookup;
    }

    public function delete(Lookup $lookup): void
    {
        $lookup->delete();
    }
}
