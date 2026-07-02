<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Validation\AddressRules;
use App\Validation\CompanyRules;
use App\Validation\CompanySettingRules;
use App\Validation\ContactRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $company = Company::query()->first();

        if (! $company) {
            return array_merge(
                CompanyRules::store(),
                ContactRules::store(),
                AddressRules::store(),
                CompanySettingRules::rules(),
            );
        }

        $existingContactIds = $company
            ->contacts()
            ->pluck('id')
            ->toArray();

        return array_merge(
            CompanyRules::update($company->id),
            ContactRules::update($existingContactIds),
            AddressRules::update(),
            CompanySettingRules::rules(),
        );
    }
}
