<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            return $next($request);
        }

        $endsAt = $tenant->subscription_ends_at;

        if ($endsAt && $endsAt->isPast()) {
            return response()->json([
                'status' => false,
                'message' => __('plan.subscription_expired'),
                'code' => 'SUBSCRIPTION_EXPIRED',
            ], 402);
        }

        return $next($request);
    }
}
