<?php

namespace App\Validation;

class AddressRules
{
    public static function store(): array
    {
        return [
            'addresses.*.address' => 'sometimes|required|string|max:255',
            'addresses.*.city_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.department_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.country_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.zip_code' => 'sometimes|nullable|string|max:20',
            'addresses.*.sector' => 'sometimes|nullable|string|max:100',
            'addresses.*.stratum_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'addresses.*.complement' => 'sometimes|nullable|string|max:255',
            'addresses.*.is_principal' => 'sometimes|required|boolean',
        ];
    }

    public static function update(): array
    {
        return [
            'addresses.*.address' => 'sometimes|required|string|max:255',
            'addresses.*.city_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.department_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.country_id' => 'sometimes|required|uuid|exists:lookups,id',
            'addresses.*.zip_code' => 'sometimes|nullable|string|max:20',
            'addresses.*.sector' => 'sometimes|nullable|string|max:100',
            'addresses.*.stratum_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'addresses.*.complement' => 'sometimes|nullable|string|max:255',
            'addresses.*.is_principal' => 'sometimes|required|boolean',
        ];
    }
}
