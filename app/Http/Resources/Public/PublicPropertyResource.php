<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicPropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $area = $this->areas->first();
        $address = $this->addresses->first();
        $coverImage = $this->images->firstWhere('is_cover', true) ?? $this->images->first();

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status ? [
                'id' => $this->status->id,
                'name' => $this->status->name,
                'alias' => $this->status->alias,
            ] : null,
            'offer_type' => $this->offerType ? [
                'id' => $this->offerType->id,
                'name' => $this->offerType->name,
                'alias' => $this->offerType->alias,
            ] : null,
            'property_type' => $this->propertyType ? [
                'id' => $this->propertyType->id,
                'name' => $this->propertyType->name,
                'alias' => $this->propertyType->alias,
            ] : null,
            'price' => $this->price ? [
                'price' => $this->price->price,
                'price_min' => $this->price->price_min,
                'price_max' => $this->price->price_max,
                'currency' => $this->price->currency,
            ] : null,
            'rooms' => $this->rooms,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area' => $area ? [
                'value' => $area->area_value,
                'unit' => $area->areaUnit?->name,
                'unit_alias' => $area->areaUnit?->alias,
                'type' => $area->areaType?->name,
                'type_alias' => $area->areaType?->alias,
            ] : null,
            'location' => $address ? [
                'address' => $address->address,
                'department' => $address->department ? [
                    'id' => $address->department->id,
                    'name' => $address->department->name,
                    'alias' => $address->department->alias,
                ] : null,
                'city' => $address->city ? [
                    'id' => $address->city->id,
                    'name' => $address->city->name,
                    'alias' => $address->city->alias,
                ] : null,
            ] : null,
            'cover_image' => $coverImage ? [
                'id' => $coverImage->id,
                'title' => $coverImage->title,
                'url' => $coverImage->url,
                'is_cover' => $coverImage->is_cover,
            ] : null,
            'images' => $this->images->take(4)->map(fn ($image): array => [
                'id' => $image->id,
                'title' => $image->title,
                'url' => $image->url,
                'sort_order' => $image->sort_order,
                'is_cover' => $image->is_cover,
            ])->values(),
            'images_count' => $this->images_count,
            'created_at' => $this->created_at?->toDateString(),
        ];
    }
}
