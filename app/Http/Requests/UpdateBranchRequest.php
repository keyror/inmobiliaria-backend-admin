<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'branch.company_name' => ['sometimes', 'string', 'max:255'],
            'branch.tradename' => ['nullable', 'string', 'max:255'],
            'branch.nit' => ['sometimes', 'string', 'max:20'],
            'branch.branch_code' => ['nullable', 'string', 'max:20'],
            'branch.is_active' => ['nullable', 'boolean'],
            'branch.legal_representative_id' => ['nullable', 'uuid', 'exists:people,id'],
            'branch.person_attendant_id' => ['nullable', 'uuid', 'exists:people,id'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.id' => ['nullable', 'uuid'],
            'contacts.*.phone' => ['nullable', 'string', 'max:30'],
            'contacts.*.mobile' => ['nullable', 'string', 'max:30'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.is_principal' => ['nullable', 'boolean'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'uuid'],
            'addresses.*.address' => ['nullable', 'string', 'max:255'],
            'addresses.*.city_id' => ['nullable', 'uuid', 'exists:lookups,id'],
            'addresses.*.department_id' => ['nullable', 'uuid', 'exists:lookups,id'],
            'addresses.*.country_id' => ['nullable', 'uuid', 'exists:lookups,id'],
            'addresses.*.is_principal' => ['nullable', 'boolean'],
        ];
    }
}
