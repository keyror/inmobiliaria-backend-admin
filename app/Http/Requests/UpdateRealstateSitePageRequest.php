<?php

namespace App\Http\Requests;

use App\Support\RealstateSiteTemplates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRealstateSitePageRequest extends FormRequest
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
            'is_active' => ['sometimes', 'boolean'],
            'template' => ['sometimes', 'string', Rule::in(RealstateSiteTemplates::TEMPLATE_SETS)],
            'content' => ['sometimes', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'is_active' => 'estado de la página',
            'template' => 'plantilla de la página',
            'content' => 'contenido',
        ];
    }
}
