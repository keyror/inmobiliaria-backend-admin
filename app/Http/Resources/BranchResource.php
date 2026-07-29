<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_company_id' => $this->parent_company_id,
            'branch_code' => $this->branch_code,
            'is_active' => $this->is_active,
            'is_headquarters' => $this->isHeadquarters(),
            'uses_branches' => $this->uses_branches,
            'company_name' => $this->company_name,
            'tradename' => $this->tradename,
            'nit' => $this->nit,
            'logo' => $this->when($this->relationLoaded('logo') && $this->logo, fn () => [
                'id' => $this->logo->id,
                'url' => $this->logo->url,
            ]),
            'contacts' => $this->whenLoaded('contacts'),
            'addresses' => $this->whenLoaded('addresses'),
            'created_at' => $this->created_at?->toDateString(),
        ];
    }
}
