<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->input('action', 'sign');

        if ($action === 'reject') {
            return [
                'action' => 'required|in:sign,reject',
                'rejection_reason' => 'required|string|max:1000',
            ];
        }

        return [
            'action' => 'required|in:sign,reject',
            'signature_type' => 'required|in:drawn,uploaded',
            'signature' => 'required|file|mimes:png,jpg,jpeg,webp|max:4096',
        ];
    }
}
