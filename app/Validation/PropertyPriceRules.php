<?php

namespace App\Validation;

class PropertyPriceRules
{
    public static function store(): array
    {
        return [
            'prices.*.price_type_id' => 'required|uuid|exists:lookups,id',
            'prices.*.price_min' => 'required|numeric|min:0',
            'prices.*.price_max' => 'required|numeric|min:0',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.currency' => 'sometimes|string|max:3',
        ];
    }

    public static function update(): array
    {
        return [
            'prices.*.price_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'prices.*.price_min' => 'sometimes|required|numeric|min:0',
            'prices.*.price_max' => 'sometimes|required|numeric|min:0',
            'prices.*.price' => 'sometimes|required|numeric|min:0',
            'prices.*.currency' => 'sometimes|string|max:3',
        ];
    }
}
