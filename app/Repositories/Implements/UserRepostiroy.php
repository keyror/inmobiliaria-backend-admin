<?php

namespace App\Repositories\Implements;

use App\Models\User;
use App\Repositories\IUserRepository;

class UserRepostiroy implements IUserRepository
{

    public function getUsersByFilters()
    {
        $users = User::query()
            ->allowedFilters(['email', 'name','created_at'])
            ->allowedSorts()
            ->jsonPaginate();
        return $users;
    }
}
