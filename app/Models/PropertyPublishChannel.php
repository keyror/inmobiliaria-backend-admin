<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPublishChannel extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'property_id',
        'channel_id',
        'external_link',
        'is_published',
        'published_at',
        'unpublished_at',
        'channel_specific_data',
    ];

    protected $casts = [
        'channel_specific_data' => 'array',
        'is_published' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'channel_id');
    }
}

