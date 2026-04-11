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

        $existingContactIds = $person
            ->contacts()
            ->pluck('id')
            ->toArray();

        return array_merge(
            PersonRules::update($person->id),
            FiscalProfileRules::update(),
            AddressRules::update(),
            ContactRules::update($existingContactIds),
            AccountBankRules::update(),
        );
    }
}
