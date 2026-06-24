<?php

namespace App\Validation;

class PropertyFeatureRules
{
    public static function store(): array
    {
        return [
            'features.*.feature_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'features.*.feature_description' => 'sometimes|nullable|string|max:500',
        ];
    }

    public static function update(): array
    {
        return [
            'features.*.feature_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'features.*.feature_description' => 'sometimes|nullable|string|max:500',
        ];
    }
}
