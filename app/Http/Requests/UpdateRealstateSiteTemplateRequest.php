<?php

namespace App\Http\Requests;

use App\Support\RealstateSiteTemplates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRealstateSiteTemplateRequest extends FormRequest
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
            'template_set' => ['required', 'string', Rule::in(RealstateSiteTemplates::TEMPLATE_SETS)],
            'theme' => ['sometimes', 'array'],
            'theme.primary' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.secondary' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.accent' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'template_set' => 'plantilla',
            'theme' => 'tema',
            'theme.primary' => 'color primario',
            'theme.secondary' => 'color secundario',
            'theme.accent' => 'color de acento',
        ];
    }
}
