<?php

namespace App\Validation;

class FiscalProfileRules
{
    public static function update(): array
    {
        return [
            'tax_regime' => 'nullable|string|max:255',
            'responsible_for_vat' => 'nullable|string|in:SI,NO',
            'vat_withholding' => 'nullable|numeric|between:0,100',
            'income_tax_withholding' => 'nullable|numeric|between:0,100',
            'ica_withholding' => 'nullable|numeric|between:0,100',
            'economic_activity' => 'nullable|string|max:255',
            'dv' => 'nullable|string|max:1',
            'taxe_type_id' => 'nullable|uuid|exists:lookups,id',
        ];
    }

    public static function store(): array
    {
        return [
            'tax_regime' => 'sometimes|required|string|max:255',
            'responsible_for_vat' => 'sometimes|nullable|string|in:SI,NO',
            'vat_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'income_tax_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'ica_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'economic_activity' => 'sometimes|nullable|string|max:255',
            'dv' => 'sometimes|nullable|string|max:1',
            'taxe_type_id' => 'sometimes|required|uuid|exists:lookups,id',
        ];
    }
}
