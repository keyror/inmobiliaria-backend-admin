<?php

namespace App\Validation;

class PropertyObligationRules
{
    public static function store(): array
    {
        return [
            'obligations.*.obligation_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.amount' => 'sometimes|required|numeric|min:0',
            'obligations.*.total' => 'sometimes|required|numeric|min:0',
            'obligations.*.frequency_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.expiration_date' => 'sometimes|nullable|date',
            'obligations.*.status_id' => 'sometimes',
            'obligations.*.description' => 'sometimes|nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'obligations.*.obligation_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.amount' => 'sometimes|required|numeric|min:0',
            'obligations.*.total' => 'sometimes|required|numeric|min:0',
            'obligations.*.frequency_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'obligations.*.expiration_date' => 'sometimes|nullable|date',
            'obligations.*.status_id' => 'sometimes',
            'obligations.*.description' => 'sometimes|nullable|string',
        ];
    }
}
