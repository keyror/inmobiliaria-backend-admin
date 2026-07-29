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
            ->with(['status', 'roles'])
            ->allowedFilters(['email', 'created_at', 'status.name'])
            ->allowedSorts()
            ->jsonPaginate();
    }

    public function createUser(StoreUserRequest $request): void
    {
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'status_type_id' => $request->status_type_id,
        ]);

        $user->syncRoles($request->roles);
        $this->syncBranches($user, $request->input('branch_ids', []), $request->input('default_branch_id'));
    }

    public function updateUser(User $user, UpdateUserRequest $request): void
    {
        $updateData = [
            'email' => $request->email,
            'status_type_id' => $request->status_type_id,
        ];

        if (! empty($request->password)) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);
        $user->syncRoles($request->roles);

        if ($request->has('branch_ids')) {
            $this->syncBranches($user, $request->input('branch_ids', []), $request->input('default_branch_id'));
        }
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function getUser(User $user): User
    {
        return $user->load([
            'roles:id',
            'companies' => function ($q) {
                $q->select(['companies.id', 'company_name', 'tradename', 'parent_company_id', 'is_active'])
                    ->withPivot('is_default');
            },
        ]);
    }

    private function syncBranches(User $user, array $branchIds, ?string $defaultId): void
    {
        if (empty($branchIds)) {
            $user->companies()->detach();

            return;
        }

        $sync = [];
        foreach ($branchIds as $id) {
            $sync[$id] = ['is_default' => $id === $defaultId];
        }

        $user->companies()->sync($sync);
    }
}
