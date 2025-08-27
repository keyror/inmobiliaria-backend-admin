<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'domain' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('domains', 'domain')->ignore($this->route('tenant'))
            ],
            'plan' => 'sometimes|nullable|string|in:basic,premium,enterprise',
            'status' => 'sometimes|nullable|string|in:active,inactive,suspended',
            'subscription_ends_at' => 'sometimes|nullable|date'
        ];
    }
}
