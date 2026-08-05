<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'max_users' => $this->max_users,
            'max_properties' => $this->max_properties,
            'max_images_per_property' => $this->max_images_per_property,
            'is_active' => $this->is_active,
            'data' => $this->data,
            'frequency' => $this->whenLoaded('frequency', fn () => [
                'id' => $this->frequency->id,
                'name' => $this->frequency->name,
                'alias' => $this->frequency->alias,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
