<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CentralDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total' => $this->resource['total'],
            'new_this_month' => $this->resource['new_this_month'],
            'expiring_soon' => $this->resource['expiring_soon'],
            'expired' => $this->resource['expired'],
            'by_plan' => $this->resource['by_plan'],
            'by_status' => $this->resource['by_status'],
            'recent' => $this->resource['recent'],
        ];
    }
}
