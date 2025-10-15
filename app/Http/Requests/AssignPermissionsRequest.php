<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'Debe seleccionar al menos un permiso.',
            'permissions.array' => 'Los permisos deben ser un arreglo.',
            'permissions.*.exists' => 'Uno o más permisos no existen.',
        ];
    }
}
