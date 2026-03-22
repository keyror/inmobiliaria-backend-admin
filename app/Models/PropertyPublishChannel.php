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
        'status_id',
        'published_at',
        'unpublished_at',
        'channel_specific_data',
    ];

    protected $casts = [
        'channel_specific_data' => 'array',
        'published_at' => 'date:Y-m-d',
        'unpublished_at' => 'date:Y-m-d',
    ];

    /** Relaciones con lookups */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'channel_id');
    }
}

