<?php

namespace App\Http\Requests;

use App\Validation\DocumentRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return DocumentRules::store();
    }
}
