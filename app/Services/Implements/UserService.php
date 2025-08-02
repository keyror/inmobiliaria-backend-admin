<?php

namespace App\Services\Implements;

use App\Models\User;
use App\Services\IUserService;
use Illuminate\Http\JsonResponse;

class UserService implements IUserService
{
    public function getUsers(): JsonResponse
    {
        $users = User::query()
            ->allowedFilters(['email', 'name','created_at'])
            ->allowedSorts()
            ->jsonPaginate();

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }
}
