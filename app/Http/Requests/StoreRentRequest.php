<?php

namespace App\Http\Requests;

use App\Validation\RentRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return RentRules::store();
    }
}
