<?php

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicPropertyIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status_id' => ['nullable', 'uuid'],
            'offer_type_id' => ['nullable', 'uuid'],
            'property_type_id' => ['nullable', 'uuid'],
            'department_id' => ['nullable', 'uuid'],
            'city_id' => ['nullable', 'uuid'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::when($this->filled('price_min'), ['gte:price_min']),
            ],
            'area_min' => ['nullable', 'numeric', 'min:0'],
            'area_max' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::when($this->filled('area_min'), ['gte:area_min']),
            ],
            'sortBy' => [
                'nullable',
                'string',
                Rule::in([
                    'created_at',
                    'price.price',
                    'title',
                ]),
            ],
            'sortType' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:60'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
