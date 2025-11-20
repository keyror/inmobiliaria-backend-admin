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
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'status_type_id' => 'sometimes',
            'role_id' => [
                'sometimes',
                'integer',
                Rule::exists('roles', 'id')
            ],
        ];
    }

    public static function store(): array
    {
        return [
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'status_type_id' => 'sometimes',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
            ],
        ];
    }

}
