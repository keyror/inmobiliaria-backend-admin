<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardRecentPropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->images->first();

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'status' => $this->status?->name,
            'offer_type' => $this->offerType?->name,
            'cover_url' => $cover?->url,
            'created_at' => $this->created_at?->toDateString(),
        ];
    }
}
