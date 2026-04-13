<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class PropertyRules
{
    public static function store(): array
    {
        return [
            'property.code' => 'nullable|string|max:255|unique:properties,code',
            'property.status_property_id' => 'required|uuid|exists:lookups,id',
            'property.status_id' => 'required|uuid|exists:lookups,id',
            'property.title' => 'required|string|max:255',
            'property.offer_type_id' => 'required|uuid|exists:lookups,id',
            'property.property_type_id' => 'required|uuid|exists:lookups,id',
            'property.social_strata' => 'nullable|string|max:255',
            'property.year_built' => 'nullable|string|max:255',
            'property.rooms' => 'nullable|string|max:255',
            'property.bedrooms' => 'nullable|string|max:255',
            'property.bathrooms' => 'nullable|string|max:255',
            'property.garage_type_id' => 'nullable|uuid|exists:lookups,id',
            'property.garage_spots' => 'nullable|string|max:255',
            'property.cadastral_number' => 'nullable|string|max:255|unique:properties,cadastral_number',
            'property.url_google_map' => 'nullable|string|max:255',
            'property.latitude' => 'nullable|numeric|between:-90,90',
            'property.longitude' => 'nullable|numeric|between:-180,180',
            'property.boundaries' => 'nullable|string',
            'property.description' => 'nullable|string',
        ];
    }

    public static function update(string $propertyId): array
    {
        return [
            'property.code' => [
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
            'property.social_strata' => 'nullable|string|max:255',
            'property.year_built' => 'nullable|string|max:255',
            'property.rooms' => 'nullable|string|max:255',
            'property.bedrooms' => 'nullable|string|max:255',
            'property.bathrooms' => 'nullable|string|max:255',
            'property.garage_type_id' => 'nullable|uuid|exists:lookups,id',
            'property.garage_spots' => 'nullable|string|max:255',
            'property.cadastral_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('properties', 'cadastral_number')->ignore($propertyId),
            ],
            'property.url_google_map' => 'nullable|string|max:255',
            'property.latitude' => 'nullable|numeric|between:-90,90',
            'property.longitude' => 'nullable|numeric|between:-180,180',
            'property.boundaries' => 'nullable|string',
            'property.description' => 'nullable|string',
        ];
    }
}
