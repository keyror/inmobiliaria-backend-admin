<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_uuid' => $this->batch_uuid,
            'log_name' => $this->log_name,
            'module_label' => __('audit.modules.'.$this->log_name, [], 'es'),
            'description' => $this->description,
            'event' => $this->event,
            'subject_type' => $this->subject_type ? class_basename($this->subject_type) : null,
            'subject_id' => $this->subject_id,
            'causer_email' => $this->causer?->email,
            'properties' => $this->properties,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
