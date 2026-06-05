<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLookupRequest extends FormRequest
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
        return [
            'category' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'between:-999999.99,999999.99'],
            'code' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'lang' => ['nullable', 'string', 'max:10'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
