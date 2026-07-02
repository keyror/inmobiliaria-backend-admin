<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Validation\AddressRules;
use App\Validation\CompanyRules;
use App\Validation\CompanySettingRules;
use App\Validation\ContactRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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

        return array_merge(
            CompanyRules::store($company?->id),
            ContactRules::store(),
            AddressRules::store(),
            CompanySettingRules::rules(),
        );
    }
}
