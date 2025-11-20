<?php

namespace App\Repositories;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPermissionRepository
{
    public function getPermissionsByFilters(): LengthAwarePaginator;
    public function create(StorePermissionRequest $request): void;
    public function update(UpdatePermissionRequest $request, Permission $permission): void;
    public function delete(Permission $permission): void;
}
