<?php

namespace App\Http\Requests;

use App\Models\ContractClause;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractClauseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'template_key' => ['required', 'string', 'max:60'],
            'section_key' => ['nullable', 'string', 'max:60'],
            'section_type' => ['required', 'string', Rule::in(ContractClause::SECTION_TYPES)],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'content_json' => ['nullable', 'array'],
            'section_config' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ];
    }
}
