<?php

namespace App\Validation;

class PropertyOwnershipRules
{
    public static function store(): array
    {
        return [
            'ownerships.*.person_id' => 'sometimes|required|uuid|exists:people,id',
            'ownerships.*.ownership_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'ownerships.*.is_primary_owner' => 'sometimes|boolean',
            'ownerships.*.ownership_start_date' => 'sometimes|nullable|date',
            'ownerships.*.ownership_end_date' => 'sometimes|nullable|date',
            'ownerships.*.status_id' => 'sometimes|nullable',
        ];
    }

    public static function update(): array
    {
        return [
            'ownerships.*.person_id' => 'sometimes|required|uuid|exists:people,id',
            'ownerships.*.ownership_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'ownerships.*.is_primary_owner' => 'sometimes|boolean',
            'ownerships.*.ownership_start_date' => 'sometimes|nullable|date',
            'ownerships.*.ownership_end_date' => 'sometimes|nullable|date',
            'ownerships.*.status_id' => 'sometimes|nullable',
        ];
    }
}
