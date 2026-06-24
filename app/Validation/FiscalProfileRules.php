<?php

namespace App\Validation;

class FiscalProfileRules
{
    public static function store(): array
    {
        return [
            'fiscal_profile.rental_fee' => 'sometimes|nullable|numeric|max:255',
            'fiscal_profile.responsible_for_vat_type_id' => 'sometimes|required|string',
            'fiscal_profile.vat_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.income_tax_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.ica_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.economic_activities' => 'sometimes|required|array|min:1',
            'fiscal_profile.economic_activities.*' => 'sometimes|required|string|max:255',
            'fiscal_profile.taxe_types' => 'sometimes|required|array|min:1',
            'fiscal_profile.taxe_types.*' => 'sometimes|required|uuid|exists:lookups,id',

        ];
    }

    public static function update(): array
    {
        return [
            'fiscal_profile.rental_fee' => 'sometimes|nullable|numeric|max:255',
            'fiscal_profile.responsible_for_vat' => 'sometimes|nullable|string',
            'fiscal_profile.vat_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.income_tax_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.ica_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.economic_activities' => 'sometimes|required|array|min:1',
            'fiscal_profile.economic_activities.*' => 'sometimes|required|string|max:255',
            'fiscal_profile.taxe_types' => 'sometimes|required|array|min:1',
            'fiscal_profile.taxe_types.*' => 'sometimes|required|uuid|exists:lookups,id',

        ];
    }
}
