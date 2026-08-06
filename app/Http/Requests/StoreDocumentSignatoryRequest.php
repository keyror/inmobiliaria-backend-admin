<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentSignatoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signatories' => 'required|array|min:1',
            'signatories.*.person_id' => 'nullable|uuid|exists:people,id',
            'signatories.*.name' => 'required|string|max:255',
            'signatories.*.email' => 'required|email|max:255',
            'signatories.*.role' => 'required|string|in:arrendatario,arrendador,codeudor,propietario,custom',
            'signatories.*.order' => 'required|integer|min:1',
        ];
    }
}
