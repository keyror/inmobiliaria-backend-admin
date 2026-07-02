<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class PropertyRules
{
    public static function store(): array
    {
        return [
            'property.code' => 'sometimes|nullable|string|max:255|unique:properties,code',
            'property.status_property_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.status_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.title' => 'sometimes|required|string|max:255',
            'property.offer_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.property_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.stratum_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'year_built' => 'sometimes|nullable|integer|min:1900|max:'.now()->year,
            'property.rooms' => 'sometimes|nullable|string|max:255',
            'property.bathrooms' => 'sometimes|nullable|string|max:255',
            'property.garage_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'property.garage_spots' => 'sometimes|nullable|string|max:255',
            'property.cadastral_number' => 'sometimes|nullable|string|max:255|unique:properties,cadastral_number',
            'property.url_google_map' => 'sometimes|nullable|string|max:255',
            'property.latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'property.longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'property.boundaries' => 'sometimes|nullable|string',
            'property.description' => 'sometimes|nullable|string',
            'property.images' => 'sometimes|nullable',
            'property.is_featured' => 'sometimes|boolean',
        ];
    }

    public static function update(string $propertyId): array
    {
        return [
            'property.code' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('properties', 'code')->ignore($propertyId),
            ],
            'property.status_property_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.status_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.title' => 'sometimes|required|string|max:255',
            'property.offer_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.property_type_id' => 'sometimes|required|uuid|exists:lookups,id',
            'property.stratum_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'year_built' => 'sometimes|nullable|integer|min:1900|max:'.now()->year,
            'property.rooms' => 'sometimes|nullable|string|max:255',
            'property.bathrooms' => 'sometimes|nullable|string|max:255',
            'property.garage_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'property.garage_spots' => 'sometimes|nullable|string|max:255',
            'property.cadastral_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('properties', 'cadastral_number')->ignore($propertyId),
            ],
            'property.url_google_map' => 'sometimes|nullable|string|max:255',
            'property.latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'property.longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'property.boundaries' => 'sometimes|nullable|string',
            'property.description' => 'sometimes|nullable|string',
            'property.images' => 'sometimes|nullable',
            'property.is_featured' => 'sometimes|boolean',
        ];
    }
}
