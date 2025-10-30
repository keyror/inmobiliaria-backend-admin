<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Validation\UserRules;
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

        return PersonRules::update($person->id);
    }
}
