<?php

namespace App\Http\Resources\Public;

use App\Models\PropertyPublishChannel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicPropertyShowResource extends JsonResource
{
    /**
     * @var array<int, string>
     */
    private const VIDEO_CHANNEL_ALIASES = [
        'YOUTUBE',
        'VIMEO',
        'DAILYMOTION',
        'WISTIA',
        'GOOGLE_DRIVE',
        'DROPBOX',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $address = $this->addresses->first();
        $coverImage = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        $videoChannel = $this->videoChannel();

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'is_featured' => (bool) $this->is_featured,
            'description' => $this->description,
            'status' => $this->lookupData($this->status),
            'offer_type' => $this->lookupData($this->offerType),
            'property_type' => $this->lookupData($this->propertyType),
            'prices' => $this->prices->map(fn ($price): array => [
                'price' => $price->price,
                'price_min' => $price->price_min,
                'price_max' => $price->price_max,
                'currency' => $price->currency,
                'price_type' => $price->priceType ? [
                    'id' => $price->priceType->id,
                    'name' => $price->priceType->name,
                    'alias' => $price->priceType->alias,
                ] : null,
            ])->values(),
            'rooms' => $this->rooms,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'areas' => $this->areas->map(fn ($area): array => [
                'id' => $area->id,
                'value' => $area->area_value,
                'unit' => $this->lookupData($area->areaUnit),
                'type' => $this->lookupData($area->areaType),
            ])->values(),
            'location' => $address ? [
                'address' => $address->address,
                'department' => $this->lookupData($address->department),
                'city' => $this->lookupData($address->city),
                'google_map_url' => $this->url_google_map,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ] : null,
            'features' => $this->features->map(fn ($feature): array => [
                'id' => $feature->id,
                'description' => $feature->feature_description,
                'type' => $this->lookupData($feature->featureType),
            ])->values(),
            'publish_channels' => $this->publishChannels->map(fn ($publishChannel): array => [
                'id' => $publishChannel->id,
                'external_link' => $publishChannel->external_link,
                'published_at' => $publishChannel->published_at?->toDateString(),
                'channel' => $this->lookupData($publishChannel->channel),
                'status' => $this->lookupData($publishChannel->status),
            ])->values(),
            'video' => $videoChannel ? $this->videoData($videoChannel) : null,
            'cover_image' => $coverImage ? [
                'id' => $coverImage->id,
                'title' => $coverImage->title,
                'url' => $coverImage->url,
            ] : null,
            'images' => $this->images->map(fn ($image): array => [
                'id' => $image->id,
                'title' => $image->title,
                'url' => $image->url,
                'sort_order' => $image->sort_order,
                'is_cover' => $image->is_cover,
            ])->values(),
            'contacts' => $this->contacts->map(fn ($contact): array => [
                'id' => $contact->id,
                'phone' => $contact->phone,
                'mobile' => $contact->mobile,
                'email' => $contact->email,
                'is_principal' => $contact->is_principal,
            ])->values(),
            'created_at' => $this->created_at?->toDateString(),
        ];
    }

    /**
     * @return array{id: string, name: string|null, alias: string|null, icon: string|null}|null
     */
    private function lookupData(mixed $lookup): ?array
    {
        if (! $lookup) {
            return null;
        }

        return [
            'id' => $lookup->id,
            'name' => $lookup->name,
            'alias' => $lookup->alias,
            'icon' => $lookup->icon,
        ];
    }

    private function videoChannel(): ?PropertyPublishChannel
    {
        return $this->publishChannels
            ->first(fn (PropertyPublishChannel $publishChannel): bool => $this->isVideoChannel($publishChannel));
    }

    private function isVideoChannel(PropertyPublishChannel $publishChannel): bool
    {
        return filled($publishChannel->external_link)
            && in_array($publishChannel->channel?->alias, self::VIDEO_CHANNEL_ALIASES, true);
    }

    /**
     * @return array{url: string|null, embed_url: string|null, thumbnail_url: null, channel: array{id: string, name: string|null, alias: string|null, icon: string|null}|null}
     */
    private function videoData(PropertyPublishChannel $publishChannel): array
    {
        $url = $publishChannel->external_link;

        return [
            'url' => $url,
            'embed_url' => $this->embedUrl($url),
            'thumbnail_url' => null,
            'channel' => $this->lookupData($publishChannel->channel),
        ];
    }

    private function embedUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return $url;
        }

        if (Str::contains($host, 'youtu.be')) {
            return 'https://www.youtube.com/embed/'.ltrim((string) parse_url($url, PHP_URL_PATH), '/');
        }

        if (Str::contains($host, 'youtube.com')) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'.$query['v'];
            }
        }

        return $url;
    }
}
