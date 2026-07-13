<?php

namespace App\Http\Requests;

use App\Models\TemplateSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTemplateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'section_type' => ['sometimes', 'string', Rule::in(TemplateSection::SECTION_TYPES)],
            'heading' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'nullable', 'string'],
            'content_json' => ['sometimes', 'nullable', 'array'],
            'section_config' => ['sometimes', 'nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
