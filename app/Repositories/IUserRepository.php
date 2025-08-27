<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface IUserRepository
{
    public function getUsersByFilters(): LengthAwarePaginator;
}
