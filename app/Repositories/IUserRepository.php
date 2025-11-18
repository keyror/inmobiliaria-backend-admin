<?php

namespace App\Repositories;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface IUserRepository
{
    public function getUsersByFilters(): LengthAwarePaginator;
    public function getUser(User $user): User;
    public function createUser(StoreUserRequest $request): void;
    public function updateUser(User $user, UpdateUserRequest $request): void;
    public function delete(User $user): void;
}
