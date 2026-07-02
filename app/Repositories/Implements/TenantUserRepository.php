<?php

namespace App\Repositories\Implements;

use App\Models\Lookup;
use App\Models\Role;
use App\Models\User;
use App\Repositories\ITenantUserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantUserRepository implements ITenantUserRepository
{
    public function getUsers(): LengthAwarePaginator
    {
        return User::query()
            ->with(['status', 'roles'])
            ->allowedFilters(['email', 'created_at', 'status.name'])
            ->allowedSorts()
            ->jsonPaginate();
    }

    public function findUser(string $userId): User
    {
        return User::with('roles:id')->findOrFail($userId);
    }

    public function createUser(array $data, array $roles): void
    {
        $user = User::create($data);
        $user->syncRoles($roles);
    }

    public function updateUser(string $userId, array $data, array $roles): void
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        $user->syncRoles($roles);
    }

    public function deleteUser(string $userId): void
    {
        User::findOrFail($userId)->delete();
    }

    public function getRoles(): Collection
    {
        return Role::query()
            ->where('guard_name', 'api')
            ->get(['id', 'name']);
    }

    public function getStatuses(): Collection
    {
        return Lookup::query()
            ->where('category', 'status')
            ->get(['id', 'name']);
    }
}
