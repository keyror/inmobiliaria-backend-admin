<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class CompanyRules
{
    public static function store(?string $companyId = null): array
    {
        return [
            'company' => 'sometimes|required|array',
            'company.company_name' => 'sometimes|required|string|max:255',
            'company.tradename' => 'sometimes|nullable|string|max:255',
            'company.nit' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'nit')->ignore($companyId),
            ],
            'company.logo_image_id' => 'sometimes|nullable|uuid|exists:images,id',
            'company.legal_representative_id' => 'sometimes|nullable|uuid|exists:people,id',
            'company.person_attendant_id' => 'sometimes|nullable|uuid|exists:people,id',
            'company.fiscal_profile_id' => 'sometimes|nullable|uuid|exists:fiscal_profiles,id',
            'contacts' => 'sometimes|nullable|array',
            'contacts.*.id' => 'sometimes|nullable|uuid|exists:contacts,id',
            'addresses' => 'sometimes|nullable|array',
            'addresses.*.id' => 'sometimes|nullable|uuid|exists:addresses,id',
        ];
    }

    public static function update(string $companyId): array
    {
        return [
            'company' => 'sometimes|required|array',
            'company.company_name' => 'sometimes|required|string|max:255',
            'company.tradename' => 'sometimes|nullable|string|max:255',
            'company.nit' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'nit')->ignore($companyId),
            ],
            'company.logo_image_id' => 'sometimes|nullable|uuid|exists:images,id',
            'company.legal_representative_id' => 'sometimes|nullable|uuid|exists:people,id',
            'company.person_attendant_id' => 'sometimes|nullable|uuid|exists:people,id',
            'company.fiscal_profile_id' => 'sometimes|nullable|uuid|exists:fiscal_profiles,id',
            'contacts' => 'sometimes|nullable|array',
            'contacts.*.id' => 'sometimes|nullable|uuid|exists:contacts,id',
            'addresses' => 'sometimes|nullable|array',
            'addresses.*.id' => 'sometimes|nullable|uuid|exists:addresses,id',
        ];
    }
}
