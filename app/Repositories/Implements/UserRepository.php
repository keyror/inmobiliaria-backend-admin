<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
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

    public function createUser(StoreUserRequest $request): void
    {
         User::create([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => $request->is_active,
        ]);

    }

    public function updateUser(User $user, UpdateUserRequest $request): void
    {
        $updateData = [
            'email' => $request->email,
            'is_active' => $request->is_active,
        ];

        if (!empty($request->password)) {
            $updateData['password'] = $request->password; // El cast lo hashea
        }

        $user->update($updateData);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
