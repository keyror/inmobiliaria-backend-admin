<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;

interface IUserRepository
{
    public function getUsersByFilters();
}
