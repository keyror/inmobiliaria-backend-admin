<?php

namespace App\Validation;

class RentRules
{
    public static function store(): array
    {
        return [
            'rent.property_id' => 'required|uuid|exists:properties,id',
            'rent.status' => 'sometimes|nullable|string|max:50',
            'rent.contract_number' => 'sometimes|nullable|string|max:100',
            'rent.contract_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.start_date' => 'required|date',
            'rent.end_date' => 'sometimes|nullable|date|after:rent.start_date',
            'rent.duration' => 'sometimes|nullable|integer|min:1',
            'rent.destination' => 'sometimes|nullable|string|max:100',
            'rent.activity' => 'sometimes|nullable|string|max:255',
            'rent.period' => 'sometimes|nullable|date',
            'rent.canon' => 'sometimes|nullable|numeric|min:0',
            'rent.iva' => 'sometimes|nullable|numeric|min:0|max:100',
            'rent.administration_included' => 'sometimes|boolean',
            'rent.is_ph' => 'sometimes|boolean',
            'rent.interest_rate' => 'sometimes|nullable|numeric|min:0|max:100',
            'rent.increment_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.adjustment_date' => 'sometimes|nullable|date',
            'rent.is_insured' => 'sometimes|boolean',
            'rent.consignment_account' => 'sometimes|nullable|string|max:100',
            'rent.payment_bank_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.commissions' => 'sometimes|nullable|string|max:255',
            'rent.signed_city' => 'sometimes|nullable|string|max:100',
            'rent.signed_at' => 'sometimes|nullable|date',
            'rent.additional_clauses' => 'sometimes|nullable|array',
            'rent.additional_clauses.*' => 'sometimes|nullable|string|max:2000',
            'rent.internal_notes' => 'sometimes|nullable|string',
            'rent.limit_dates_id' => 'sometimes|nullable|uuid|exists:limit_dates,id',

            'rent_tenants' => 'sometimes|array',
            'rent_tenants.*.tenant_id' => 'required_with:rent_tenants|uuid|exists:people,id',
            'rent_tenants.*.codebtor_id' => 'sometimes|nullable|uuid|exists:people,id',
        ];
    }

    public static function update(): array
    {
        return [
            'rent.property_id' => 'sometimes|required|uuid|exists:properties,id',
            'rent.status' => 'sometimes|nullable|string|max:50',
            'rent.contract_number' => 'sometimes|nullable|string|max:100',
            'rent.contract_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.start_date' => 'sometimes|required|date',
            'rent.end_date' => 'sometimes|nullable|date',
            'rent.duration' => 'sometimes|nullable|integer|min:1',
            'rent.destination' => 'sometimes|nullable|string|max:100',
            'rent.activity' => 'sometimes|nullable|string|max:255',
            'rent.period' => 'sometimes|nullable|date',
            'rent.canon' => 'sometimes|nullable|numeric|min:0',
            'rent.iva' => 'sometimes|nullable|numeric|min:0|max:100',
            'rent.administration_included' => 'sometimes|boolean',
            'rent.is_ph' => 'sometimes|boolean',
            'rent.interest_rate' => 'sometimes|nullable|numeric|min:0|max:100',
            'rent.increment_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.adjustment_date' => 'sometimes|nullable|date',
            'rent.is_insured' => 'sometimes|boolean',
            'rent.consignment_account' => 'sometimes|nullable|string|max:100',
            'rent.payment_bank_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'rent.commissions' => 'sometimes|nullable|string|max:255',
            'rent.signed_city' => 'sometimes|nullable|string|max:100',
            'rent.signed_at' => 'sometimes|nullable|date',
            'rent.additional_clauses' => 'sometimes|nullable|array',
            'rent.additional_clauses.*' => 'sometimes|nullable|string|max:2000',
            'rent.internal_notes' => 'sometimes|nullable|string',
            'rent.limit_dates_id' => 'sometimes|nullable|uuid|exists:limit_dates,id',

            'rent_tenants' => 'sometimes|array',
            'rent_tenants.*.tenant_id' => 'required_with:rent_tenants|uuid|exists:people,id',
            'rent_tenants.*.codebtor_id' => 'sometimes|nullable|uuid|exists:people,id',
        ];
    }
}
