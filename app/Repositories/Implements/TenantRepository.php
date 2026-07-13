<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Repositories\ILookupRepository;
use App\Repositories\ITenantRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Stancl\Tenancy\Jobs\DeleteDatabase;

class TenantRepository implements ITenantRepository
{
    public function __construct(
        private ILookupRepository $lookupRepository
    ) {}

    public function getTenantsByFilters(): LengthAwarePaginator
    {
        $tenants = Tenant::query()
            ->with('domains', 'status', 'plan')
            ->allowedFilters([
                'email',
                'name',
                'plan.name',
                'status.name',
                'created_at',
                'subscription_ends_at',
            ])
            ->allowedSorts()
            ->jsonPaginate();

        return $tenants;
    }

    public function getTenant(Tenant $tenant): Tenant
    {
        return $tenant->load([
            'domains',
            'status:id,name',
            'plan:id,name',
        ]);
    }

    public function create(StoreTenantRequest $request): Tenant
    {
        $lookups = $this->lookupRepository->getLookupsByCategory(categories: ['status']);

        $pendiente = $lookups['status']
            ->firstWhere('name', 'PENDIENTE');

        $id = Str::uuid();
        $tenant = Tenant::create([
            'id' => $id,
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'plan_id' => $request->plan_id,
            'status_id' => $pendiente->id,
            'subscription_ends_at' => $request->subscription_ends_at,
            'tenancy_db_name' => 'realstate_'.
                strtolower(
                    implode(
                        '',
                        array_map(
                            fn ($w) => $w[0], explode(' ', $request->name)
                        )
                    )
                ).'_'.$id,

        ]);

        $tenant->createDomain([
            'domain' => $request->domain,
        ]);

        return $tenant;
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): void
    {
        $oldDomain = $tenant->domain;
        $newDomain = $request->domain;

        if ($newDomain && $newDomain !== $oldDomain) {
            $tenant->domains()->delete();
        }

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $newDomain,
            'plan_id' => $request->plan_id,
            'status_id' => $request->status_id,
            'subscription_ends_at' => $request->subscription_ends_at,
        ]);

        if ($newDomain && $newDomain !== $oldDomain) {
            $tenant->createDomain([
                'domain' => $newDomain,
            ]);
        }
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
        $lookups = $this->lookupRepository->getLookupsByCategory(categories: ['status']);

        $activo = $lookups['status']
            ->firstWhere('name', 'ACTIVO');

        $tenant->update([
            'status_id' => $activo->id,
        ]);
    }

    public function deactivate(Tenant $tenant): void
    {
        $lookups = $this->lookupRepository->getLookupsByCategory(categories: ['status']);

        $inactivo = $lookups['status']
            ->firstWhere('name', 'INACTIVO');

        $tenant->update([
            'status_id' => $inactivo->id,
        ]);
    }
}
