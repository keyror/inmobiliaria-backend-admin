<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class PersonRules
{
    public static function update(string $personId): array
    {
        return [
            'person.user_id' => 'sometimes|uuid|exists:users,id',
            'person.fiscal_profile_id' => 'sometimes|uuid|exists:fiscal_profiles,id',
            'person.first_name' => 'sometimes|required|string|max:255',
            'person.last_name' => 'sometimes|required|string|max:255',
            'person.company_name' => 'sometimes|required|string|max:255',
            'person.document_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'person.document_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('people', 'document_number')->ignore($personId),
            ],
            'person.document_from' => 'sometimes|required|string|max:255',
            'person.organization_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'person.birth_date' => 'sometimes|nullable|date',
            'person.gender' => 'sometimes|nullable|string',
        ];
    }

    public static function store(): array
    {
        return [
            'person.user_id' => 'sometimes|uuid|exists:users,id',
            'person.fiscal_profile_id' => 'sometimes|uuid|exists:fiscal_profiles,id',
            'person.first_name' => 'sometimes|required|string|max:255',
            'person.last_name' => 'sometimes|required|string|max:255',
            'person.company_name' => 'sometimes|required|string|max:255',
            'person.document_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'person.document_number' => 'sometimes|required|string|max:255|unique:people,document_number',
            'person.document_from' => 'sometimes|required|string|max:255',
            'person.organization_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'person.birth_date' => 'sometimes|nullable|date',
            'person.gender' => 'sometimes|nullable|string',
        ];
    }
}
