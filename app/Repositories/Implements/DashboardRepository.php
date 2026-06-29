<?php

namespace App\Repositories\Implements;

use App\Models\Person;
use App\Models\Property;
use App\Repositories\IDashboardRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements IDashboardRepository
{
    public function getStats(): array
    {
        $byOfferType = DB::table('properties')
            ->join('lookups', 'properties.offer_type_id', '=', 'lookups.id')
            ->whereNull('properties.deleted_at')
            ->selectRaw('lookups.name as name, count(*) as total')
            ->groupBy('lookups.id', 'lookups.name')
            ->get()
            ->map(fn ($row) => ['name' => $row->name, 'total' => (int) $row->total])
            ->values()
            ->all();

        $byStatus = DB::table('properties')
            ->join('lookups', 'properties.status_id', '=', 'lookups.id')
            ->whereNull('properties.deleted_at')
            ->selectRaw('lookups.name as name, count(*) as total')
            ->groupBy('lookups.id', 'lookups.name')
            ->get()
            ->map(fn ($row) => ['name' => $row->name, 'total' => (int) $row->total])
            ->values()
            ->all();

        return [
            'total_properties' => Property::query()->count(),
            'total_people' => Person::query()->count(),
            'featured_properties' => Property::query()->where('is_featured', true)->count(),
            'properties_this_month' => Property::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'by_offer_type' => $byOfferType,
            'by_status' => $byStatus,
        ];
    }

    public function getRecentProperties(int $limit = 5): Collection
    {
        return Property::query()
            ->with([
                'status:id,name',
                'offerType:id,name',
                'images' => fn ($q) => $q->where('is_cover', true)
                    ->select('id', 'imageable_id', 'imageable_type', 'file_path'),
            ])
            ->latest()
            ->limit($limit)
            ->get(['id', 'code', 'title', 'status_id', 'offer_type_id', 'created_at']);
    }
}
