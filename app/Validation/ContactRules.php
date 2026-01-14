<?php

namespace App\Validation;

class ContactRules
{
    public static function store(): array
    {
        return [
            'contacts.*.email' => 'sometimes|required|email|max:255',
            'contacts.*.phone' => 'sometimes|required|string|max:50',
            'contacts.*.mobile' => 'sometimes|required|string|max:50',
            'contacts.*.is_principal' => 'sometimes|required|boolean',
        ];
    }

    public static function update(): array
    {
        return [
            'contacts.*.email' => 'sometimes|required|email|max:255',
            'contacts.*.phone' => 'sometimes|required|string|max:50',
            'contacts.*.mobile' => 'sometimes|required|string|max:50',
            'contacts.*.is_principal' => 'sometimes|required|boolean',
        ];
    }
}
