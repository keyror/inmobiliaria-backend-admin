<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'columns' => ['sometimes', 'required', 'array', 'min:1'],
            'columns.*.key' => ['required', 'string'],
            'columns.*.label' => ['required', 'string', 'max:80'],
            'is_default' => ['boolean'],
        ];
    }
}
