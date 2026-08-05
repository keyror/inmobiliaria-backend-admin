<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCentralSiteThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'theme' => ['required', 'array'],
            'theme.primary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.secondary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.accent' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'theme' => 'tema',
            'theme.primary' => 'color primario',
            'theme.secondary' => 'color secundario',
            'theme.accent' => 'color de acento',
        ];
    }
}
