<?php

namespace App\Validation;

class PropertyPublishChannelRules
{
    public static function store(): array
    {
        return [
            'publish_channels.*.channel_id' => 'nullable|uuid|exists:lookups,id',
            'publish_channels.*.external_link' => 'nullable|string|max:500',
            'publish_channels.*.status_id' => 'nullable',
            'publish_channels.*.published_at' => 'nullable|date',
            'publish_channels.*.unpublished_at' => 'nullable|date',
        ];
    }

    public static function update(): array
    {
        return [
            'publish_channels.*.channel_id' => 'nullable|required|uuid|exists:lookups,id',
            'publish_channels.*.external_link' => 'nullable|string|max:500',
            'publish_channels.*.status_id' => 'nullable',
            'publish_channels.*.published_at' => 'nullable|date',
            'publish_channels.*.unpublished_at' => 'nullable|date',
        ];
    }
}
