<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IUserService
{
    public function getUsers(): JsonResponse;
}
