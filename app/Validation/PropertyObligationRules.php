<?php

namespace App\Validation;

class PropertyObligationRules
{
    public static function store(): array
    {
        return [
            'obligations.*.obligation_type_id' => 'required|uuid|exists:lookups,id',
            'obligations.*.amount' => 'required|numeric|min:0',
            'obligations.*.total' => 'required|numeric|min:0',
            'obligations.*.frequency_type_id' => 'required|uuid|exists:lookups,id',
            'obligations.*.expiration_date' => 'nullable|date',
            'obligations.*.status_id' => 'sometimes',
            'obligations.*.description' => 'nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'obligations.*.obligation_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.amount' => 'sometimes|required|numeric|min:0',
            'obligations.*.total' => 'sometimes|required|numeric|min:0',
            'obligations.*.frequency_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.expiration_date' => 'nullable|date',
            'obligations.*.status_id' => 'sometimes',
            'obligations.*.description' => 'nullable|string',
        ];
    }
}
