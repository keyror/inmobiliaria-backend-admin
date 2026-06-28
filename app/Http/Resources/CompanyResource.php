<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_name' => $this->company_name,
            'tradename' => $this->tradename,
            'nit' => $this->nit,
            'logo' => $this->relationLoaded('logo')
                ? $this->imageData($this->logo)
                : null,
            'legal_representative' => $this->relationLoaded('legalRepresentative')
                ? $this->personData($this->legalRepresentative)
                : null,
            'person_attendant' => $this->relationLoaded('personAttendant')
                ? $this->personData($this->personAttendant)
                : null,
            'fiscal_profile' => $this->whenLoaded('fiscalProfile'),
            'contacts' => $this->whenLoaded('contacts'),
            'addresses' => $this->whenLoaded('addresses'),
            'publish_channels' => $this->whenLoaded('publishChannels', fn () => $this->publishChannels->map(fn ($pc) => [
                'id' => $pc->id,
                'channel_id' => $pc->channel_id,
                'external_link' => $pc->external_link,
                'status_id' => $pc->status_id,
                'published_at' => $pc->published_at?->toDateString(),
                'unpublished_at' => $pc->unpublished_at?->toDateString(),
            ])->all()),
            'created_at' => $this->created_at?->toDateString(),
            'updated_at' => $this->updated_at?->toDateString(),
        ];
    }

    /**
     * @return array{id: string, title: string|null, url: string|null}|null
     */
    private function imageData(mixed $image): ?array
    {
        if (! $image) {
            return null;
        }

        return [
            'id' => $image->id,
            'title' => $image->title,
            'url' => $image->url,
        ];
    }

    /**
     * @return array{id: string, full_name: string|null, document_number: string|null}|null
     */
    private function personData(mixed $person): ?array
    {
        if (! $person) {
            return null;
        }

        return [
            'id' => $person->id,
            'full_name' => $person->full_name,
            'document_number' => $person->document_number,
        ];
    }
}
