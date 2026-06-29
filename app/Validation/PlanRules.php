<?php

namespace App\Validation;

class PlanRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:100|unique:plans,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'max_users' => 'required|integer|min:1|max:65535',
            'max_properties' => 'required|integer|min:1|max:65535',
            'max_images_per_property' => 'required|integer|min:1|max:255',
            'is_active' => 'boolean',
            'data' => 'nullable|array',
        ];
    }

    public static function update(string $planId): array
    {
        return [
            'name' => "required|string|max:100|unique:plans,name,{$planId}",
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'max_users' => 'required|integer|min:1|max:65535',
            'max_properties' => 'required|integer|min:1|max:65535',
            'max_images_per_property' => 'required|integer|min:1|max:255',
            'is_active' => 'boolean',
            'data' => 'nullable|array',
        ];
    }
}
