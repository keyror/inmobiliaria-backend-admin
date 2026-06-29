<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'stats' => $this->resource['stats'],
            'recent_properties' => DashboardRecentPropertyResource::collection(
                $this->resource['recent_properties']
            ),
        ];
    }
}
