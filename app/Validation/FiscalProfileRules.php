<?php

namespace App\Validation;

class FiscalProfileRules
{
    public static function store(): array
    {
        return [
            'fiscal_profile.rental_fee' => 'sometimes|nullable|string|max:255',
            'fiscal_profile.responsible_for_vat_type_id' => 'required|string',
            'fiscal_profile.vat_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.income_tax_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.ica_withholding' => 'sometimes|nullable|numeric|between:0,100',
            'fiscal_profile.economic_activities' => 'required|array|min:1',
            'fiscal_profile.economic_activities.*' => 'required|string|max:255',
            'fiscal_profile.taxe_types' => 'required|array|min:1',
            'fiscal_profile.taxe_types.*' => 'required|uuid|exists:lookups,id',

        ];
    }

    public static function update(): array
    {
        return [
            'fiscal_profile.rental_fee' => 'nullable|string|max:255',
            'fiscal_profile.responsible_for_vat' => 'nullable|string',
            'fiscal_profile.vat_withholding' => 'nullable|numeric|between:0,100',
            'fiscal_profile.income_tax_withholding' => 'nullable|numeric|between:0,100',
            'fiscal_profile.ica_withholding' => 'nullable|numeric|between:0,100',
            'fiscal_profile.economic_activities' => 'required|array|min:1',
            'fiscal_profile.economic_activities.*' => 'required|string|max:255',
            'fiscal_profile.taxe_types' => 'required|array|min:1',
            'fiscal_profile.taxe_types.*' => 'required|uuid|exists:lookups,id',

        ];
    }
}
