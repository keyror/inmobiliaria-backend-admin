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
                Rule::unique('tenants', 'domain')->ignore($this->route('tenant'))
            ],
            'plan' => 'sometimes|nullable|string|in:BASIC,PREMIUM,ENTERPRISE',
            'status' => 'sometimes|nullable|string|in:ACTIVE,INACTIVE,SUSPENDED,EXPIRED,CANCELLED',
            'subscription_ends_at' => 'sometimes|nullable|date'
        ];
    }
}
