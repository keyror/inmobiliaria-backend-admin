<?php

namespace App\Validation;

class AddressRules
{
    public static function store(): array
    {
        return [
            'addresses.*.address' => 'required|string|max:255',
            'addresses.*.city_id' => 'required|uuid|exists:lookups,id',
            'addresses.*.department_id' => 'required|uuid|exists:lookups,id',
            'addresses.*.country_id' => 'required|uuid|exists:lookups,id',
            'addresses.*.zip_code' => 'nullable|string|max:20',
            'addresses.*.sector' => 'nullable|string|max:100',
            'addresses.*.stratum_id' => 'nullable|uuid|exists:lookups,id',
            'addresses.*.complement' => 'nullable|string|max:255',
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
            'addresses.*.zip_code' => 'nullable|string|max:20',
            'addresses.*.sector' => 'nullable|string|max:100',
            'addresses.*.stratum_id' => 'nullable|uuid|exists:lookups,id',
            'addresses.*.complement' => 'nullable|string|max:255',
            'addresses.*.is_principal' => 'sometimes|required|boolean',
        ];
    }
}
