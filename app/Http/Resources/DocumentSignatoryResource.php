<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentSignatoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'person_id' => $this->person_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'order' => $this->order,
            'status' => $this->status,
            'viewed_at' => $this->viewed_at?->toDateTimeString(),
            'signed_at' => $this->signed_at?->toDateTimeString(),
            'signature_type' => $this->signature_type,
            'rejection_reason' => $this->rejection_reason,
            'token_expires_at' => $this->token_expires_at?->toDateTimeString(),
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'view_ip_address' => $this->view_ip_address,
            'view_user_agent' => $this->view_user_agent,
            'consent_accepted_at' => $this->consent_accepted_at?->toDateTimeString(),
            'document_hash_at_signing' => $this->document_hash_at_signing,
            'sent_at' => $this->sent_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
