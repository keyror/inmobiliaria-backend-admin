<?php

namespace App\Repositories\Implements;

use App\Models\Plan;
use App\Models\Tenant;
use App\Repositories\ICentralDashboardRepository;
use Illuminate\Support\Facades\DB;

class CentralDashboardRepository implements ICentralDashboardRepository
{
    public function getStats(): array
    {
        $byPlan = Plan::withCount('tenants')
            ->orderByDesc('tenants_count')
            ->get()
            ->map(fn ($p) => ['name' => $p->name, 'count' => $p->tenants_count]);

        $byStatus = Tenant::select('l.name as status', DB::raw('count(*) as count'))
            ->join('lookups as l', 'l.id', '=', 'tenants.status_id')
            ->groupBy('l.name')
            ->pluck('count', 'status');

        $recent = Tenant::with('plan:id,name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'email', 'domain', 'plan_id', 'subscription_ends_at', 'created_at']);

        return [
            'total' => Tenant::count(),
            'new_this_month' => Tenant::where('created_at', '>=', now()->startOfMonth())->count(),
            'expiring_soon' => Tenant::whereBetween('subscription_ends_at', [now(), now()->addDays(30)])->count(),
            'expired' => Tenant::where('subscription_ends_at', '<', now())->count(),
            'by_plan' => $byPlan,
            'by_status' => $byStatus,
            'recent' => $recent,
        ];
    }
}
