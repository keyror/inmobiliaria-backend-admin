<?php

namespace App\Http\Requests;

use App\Validation\FiscalProfileRules;
use App\Validation\PersonRules;
use App\Validation\UserRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return PersonRules::store();
    }
}
