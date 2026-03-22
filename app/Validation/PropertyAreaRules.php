<?php

namespace App\Validation;

class PropertyAreaRules
{
    public static function store(): array
    {
        return [
            'areas.*.area_type_id' => 'required|uuid|exists:lookups,id',
            'areas.*.area_value' => 'required|numeric|min:0',
            'areas.*.area_unit_id' => 'required|uuid|exists:lookups,id',
        ];
    }

    public static function update(): array
    {
        return [
            'areas.*.area_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'areas.*.area_value' => 'sometimes|required|numeric|min:0',
            'areas.*.area_unit_id' => 'sometimes|required|uuid|exists:lookups,id',
        ];
    }
}
