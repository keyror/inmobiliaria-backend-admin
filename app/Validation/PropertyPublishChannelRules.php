<?php

namespace App\Validation;

class PropertyPublishChannelRules
{
    public static function store(): array
    {
        return [
            'publish_channels.*.channel_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'publish_channels.*.external_link' => 'sometimes|nullable|string|max:500',
            'publish_channels.*.status_id' => 'sometimes|nullable',
            'publish_channels.*.published_at' => 'sometimes|nullable|date',
            'publish_channels.*.unpublished_at' => 'sometimes|nullable|date',
        ];
    }

    public static function update(): array
    {
        return [
            'publish_channels.*.channel_id' => 'sometimes|nullable|required|uuid|exists:lookups,id',
            'publish_channels.*.external_link' => 'sometimes|nullable|string|max:500',
            'publish_channels.*.status_id' => 'sometimes|nullable',
            'publish_channels.*.published_at' => 'sometimes|nullable|date',
            'publish_channels.*.unpublished_at' => 'sometimes|nullable|date',
        ];
    }
}
