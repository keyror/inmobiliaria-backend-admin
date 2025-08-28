<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tenants,name',
            'email' => 'required|email|max:255|unique:tenants,email',
            'domain' => 'required|string|max:255|unique:domains,domain',
            'plan' => 'sometimes|nullable|string|in:BASIC,PREMIUM,ENTERPRISE',
            'status' => 'sometimes|nullable|string|in:ACTIVE,INACTIVE,SUSPENDED,EXPIRED,CANCELLED',
            'subscription_ends_at' => 'nullable|date'
        ];
    }
}
