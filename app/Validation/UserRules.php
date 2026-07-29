<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class UserRules
{
    public static function update(string $userId): array
    {
        return [
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'status_type_id' => 'sometimes|uuid|exists:lookups,id',
            'roles' => 'sometimes|array',
            'roles.*' => [
                'sometimes',
                'uuid',
                Rule::exists('roles', 'id'),
            ],
            'branch_ids' => 'sometimes|nullable|array',
            'branch_ids.*' => 'uuid|exists:companies,id',
            'default_branch_id' => 'sometimes|nullable|uuid|exists:companies,id',
        ];
    }

    public static function store(): array
    {
        return [
            'email' => 'sometimes|required|email|unique:users,email|max:255',
            'password' => 'sometimes|required|string|min:8|confirmed',
            'status_type_id' => 'sometimes|uuid|exists:lookups,id',
            'roles' => 'sometimes|required|array|min:1',
            'roles.*' => [
                'sometimes',
                'uuid',
                Rule::exists('roles', 'id'),
            ],
            'branch_ids' => 'sometimes|nullable|array',
            'branch_ids.*' => 'uuid|exists:companies,id',
            'default_branch_id' => 'sometimes|nullable|uuid|exists:companies,id',
        ];
    }
}
