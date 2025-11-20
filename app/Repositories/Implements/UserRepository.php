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
        return User::query()
            ->with(['status','roles'])
            ->allowedFilters(['email','created_at','status.name'])
            ->allowedSorts()
            ->jsonPaginate();
    }

    public function createUser(StoreUserRequest $request): void
    {
         $user = User::create([
             'email' => $request->email,
             'password' => $request->password,
             'status_type_id' => $request->status_type_id
        ]);

         $user->syncRoles($request->roles);
    }

    public function updateUser(User $user, UpdateUserRequest $request): void
    {
        $updateData = [
            'email' => $request->email,
            'status_type_id' => $request->status_type_id
        ];

        if (!empty($request->password)) {
            $updateData['password'] = $request->password; // El cast lo hashea
        }

        $user->update($updateData);
        $user->syncRoles($request->roles);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function getUser(User $user): User
    {
       return $user->load('roles:id');
    }
}
