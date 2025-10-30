<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class PersonRules
{
    public static function update(int $personId): array
    {
        return [
            'user_id' => 'sometimes|uuid|exists:users,id',
            'fiscal_profile_id' => 'sometimes|uuid|exists:fiscal_profiles,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|required|string|max:255',
            'document_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'document_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('people', 'document_number')->ignore($personId)
            ],
            'document_from' => 'sometimes|required|string|max:255',
            'organization_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'birth_date' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|string|in:M,F,OTRO',
        ];
    }

    public static function store(): array
    {
        return [
            'user_id' => 'sometimes|uuid|exists:users,id',
            'fiscal_profile_id' => 'sometimes|uuid|exists:fiscal_profiles,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|required|string|max:255',
            'document_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'document_number' => 'sometimes|required|string|max:255|unique:people,document_number',
            'document_from' => 'sometimes|required|string|max:255',
            'organization_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'birth_date' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|string',
        ];
    }
}
