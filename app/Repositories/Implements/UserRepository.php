<?php

namespace App\Repositories\Implements;

use App\Models\User;
use App\Repositories\IUserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements IUserRepository
{

    public function getUsersByFilters(): LengthAwarePaginator
    {
        $users = User::query()
            ->allowedFilters(['email', 'name','created_at'])
            ->allowedSorts()
            ->jsonPaginate();
        return $users;
    }
}
