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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email',
            'domain' => 'required|string|max:255|unique:domains,domain',
            'plan' => 'nullable|string|in:basic,premium,enterprise',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'subscription_ends_at' => 'nullable|date'
        ];
    }
}
