<?php

namespace App\Http\Requests;

use App\Validation\AccountBankRules;
use App\Validation\AddressRules;
use App\Validation\ContactRules;
use Illuminate\Foundation\Http\FormRequest;
use App\Validation\PersonRules;
use App\Validation\FiscalProfileRules;


class UpdatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $person = $this->route('person');

        return [
            'person' => PersonRules::update($person->id),
            'fiscal_profile' => FiscalProfileRules::update(),
            'addresses' => AddressRules::update(),
            'contacts' => ContactRules::update(),
            'account_banks' => AccountBankRules::update(),
        ];
    }
}
