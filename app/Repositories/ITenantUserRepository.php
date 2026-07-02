<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ITenantUserRepository
{
    public function getUsers(): LengthAwarePaginator;

    public function findUser(string $userId): User;

    public function createUser(array $data, array $roles): void;

    public function updateUser(string $userId, array $data, array $roles): void;

    public function deleteUser(string $userId): void;

    public function getRoles(): Collection;

    public function getStatuses(): Collection;
}
