<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class ContactRules
{
    public static function store(): array
    {
        return [
            'contacts.*.mobile' => 'sometimes|required|string|max:50',
            'contacts.*.email' => [
                'required',
                'email',
                Rule::unique('contacts', 'email'),
            ],
            'contacts.*.phone' => 'sometimes|required|string|max:50',
            'contacts.*.is_principal' => 'sometimes|required|boolean',
        ];
    }

    public static function update(array $existingIds = []): array
    {
        return [
            'contacts.*.mobile' => 'sometimes|required|string|max:50',
            'contacts.*.email' => [
                'sometimes',
                'required',
                'email',
                'distinct',
                Rule::unique('contacts', 'email')->whereNotIn('id', $existingIds),
            ],
            'contacts.*.phone' => 'sometimes|required|string|max:50',
            'contacts.*.is_principal' => 'sometimes|required|boolean',
        ];
    }
}
