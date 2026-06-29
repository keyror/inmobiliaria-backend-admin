<?php

namespace App\Http\Requests;

use App\Validation\PlanRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return PlanRules::store();
    }
}
