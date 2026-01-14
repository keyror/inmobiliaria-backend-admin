<?php

namespace App\Http\Requests;

use App\Validation\AccountBankRules;
use App\Validation\AddressRules;
use App\Validation\ContactRules;
use App\Validation\FiscalProfileRules;
use App\Validation\PersonRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            PersonRules::store(),
            FiscalProfileRules::store(),
            AddressRules::store(),
            ContactRules::store(),
            AccountBankRules::store(),
        );
    }
}
