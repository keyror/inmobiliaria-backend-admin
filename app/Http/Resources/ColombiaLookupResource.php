<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColombiaLookupResource extends JsonResource
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
            'name' => $this->name,
            'alias' => $this->alias,
            'code' => $this->code,
            'departments' => $this->whenLoaded('departments', fn (): array => $this->departments
                ->map(fn (mixed $department): array => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'alias' => $department->alias,
                    'code' => $department->code,
                    'cities' => $department->relationLoaded('cities')
                        ? $department->cities
                            ->map(fn (mixed $city): array => [
                                'id' => $city->id,
                                'name' => $city->name,
                                'alias' => $city->alias,
                                'code' => $city->code,
                            ])
                            ->values()
                            ->all()
                        : [],
                ])
                ->values()
                ->all()),
        ];
    }
}
