<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Repositories\ITenantRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Stancl\Tenancy\Jobs\DeleteDatabase;

class TenantRepository implements ITenantRepository
{
    public function getTenantsByFilters(): LengthAwarePaginator
    {
        $tenants = Tenant::query()
            ->with('domains')
            ->allowedFilters(['email', 'name', 'plan', 'status', 'created_at'])
            ->allowedSorts()
            ->jsonPaginate();

        return $tenants;
    }

    public function getTenant(Tenant $tenant): Tenant
    {
        return $tenant->load('domains');
    }

    public function create(StoreTenantRequest $request): void
    {
        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'plan' => $request->plan,
            'status' => $request->status,
            'subscription_ends_at' => $request->subscription_ends_at,
            'tenancy_db_name' => 'realstate_'.strtolower(str_replace(' ', '_', $request->name)),
        ]);

        $tenant->createDomain([
            'domain' => $request->domain
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): void
    {
        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'plan' => $request->plan,
            'status' => $request->status,
            'subscription_ends_at' => $request->subscription_ends_at,
        ]);

        $tenant->createDomain([
            'domain' => $request->domain
        ]);
    }

    public function delete(Tenant $tenant): void
    {
        // Eliminar base de datos
        DeleteDatabase::dispatch($tenant);

        // Eliminar dominios
        $tenant->domains()->delete();

        // Eliminar tenant
        $tenant->delete();
    }

    public function activate(Tenant $tenant): void
    {
        $tenant->update([
            'status' => 'active'
        ]);
    }

    public function deactivate(Tenant $tenant): void
    {
        $tenant->update([
            'status' => 'inactive'
        ]);
    }
}
