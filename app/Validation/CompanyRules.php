<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class CompanyRules
{
    public static function store(?string $companyId = null): array
    {
        return [
            'company' => 'required|array',
            'company.company_name' => 'required|string|max:255',
            'company.tradename' => 'nullable|string|max:255',
            'company.nit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'nit')->ignore($companyId),
            ],
            'company.logo_image_id' => 'nullable|uuid|exists:images,id',
            'company.legal_representative_id' => 'nullable|uuid|exists:people,id',
            'company.person_attendant_id' => 'nullable|uuid|exists:people,id',
            'company.fiscal_profile_id' => 'nullable|uuid|exists:fiscal_profiles,id',
            'contacts' => 'nullable|array',
            'contacts.*.id' => 'nullable|uuid|exists:contacts,id',
            'addresses' => 'nullable|array',
            'addresses.*.id' => 'nullable|uuid|exists:addresses,id',
        ];
    }

    public static function update(string $companyId): array
    {
        return [
            'company' => 'sometimes|required|array',
            'company.company_name' => 'sometimes|required|string|max:255',
            'company.tradename' => 'nullable|string|max:255',
            'company.nit' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'nit')->ignore($companyId),
            ],
            'company.logo_image_id' => 'nullable|uuid|exists:images,id',
            'company.legal_representative_id' => 'nullable|uuid|exists:people,id',
            'company.person_attendant_id' => 'nullable|uuid|exists:people,id',
            'company.fiscal_profile_id' => 'nullable|uuid|exists:fiscal_profiles,id',
            'contacts' => 'nullable|array',
            'contacts.*.id' => 'nullable|uuid|exists:contacts,id',
            'addresses' => 'nullable|array',
            'addresses.*.id' => 'nullable|uuid|exists:addresses,id',
        ];
    }
}
