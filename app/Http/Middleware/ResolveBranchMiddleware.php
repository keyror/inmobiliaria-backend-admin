<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveBranchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $hq = Company::whereNull('parent_company_id')
            ->select(['id', 'uses_branches'])
            ->first();

        $scopingActive = $hq?->uses_branches ?? false;

        $companyId = $scopingActive
            ? $this->resolveCompanyId($request->user(), $request->header('X-Company-Id'), $hq?->id)
            : $hq?->id;

        $request->attributes->set('current_company_id', $companyId);
        $request->attributes->set('branch_scoping_active', $scopingActive);

        return $next($request);
    }

    private function resolveCompanyId(mixed $user, ?string $requestedId, ?string $hqId): ?string
    {
        if ($user?->can('companies.view_all')) {
            if ($requestedId) {
                return Company::where('id', $requestedId)->exists() ? $requestedId : $hqId;
            }

            return $hqId;
        }

        if ($requestedId && $user?->can('companies.switch')) {
            $hasAccess = $user->companies()->where('companies.id', $requestedId)->exists();

            return $hasAccess ? $requestedId : $this->defaultBranchForUser($user, $hqId);
        }

        return $this->defaultBranchForUser($user, $hqId);
    }

    private function defaultBranchForUser(mixed $user, ?string $hqId): ?string
    {
        if (! $user) {
            return $hqId;
        }

        $default = $user->companies()->wherePivot('is_default', true)->value('companies.id');

        return $default ?? $hqId;
    }
}
