<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class UserRules
{
    public static function update(int $userId): array
    {
        return [
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:8',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public static function store(): array
    {
        return [
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'is_active' => 'sometimes|boolean',
        ];
    }

}
