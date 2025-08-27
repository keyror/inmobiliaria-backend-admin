<?php

namespace App\Repositories;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

interface ITenantRepository
{
    public function getTenantsByFilters(): LengthAwarePaginator;
    public function getTenant(Tenant $tenant): Tenant;
    public function create(StoreTenantRequest $request): void;
    public function update(UpdateTenantRequest $request, Tenant $tenant): void;
    public function delete(Tenant $tenant): void;
    public function activate(Tenant $tenant): void ;
    public function deactivate(Tenant $tenant): void ;
}
