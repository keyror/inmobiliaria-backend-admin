<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LookupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'name' => $this->name,
            'alias' => $this->alias,
            'value' => $this->value,
            'code' => $this->code,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'lang' => $this->lang,
        ];
    }
}
