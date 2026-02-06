<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyFeature extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'property_id',
        'feature_type_id',
        'feature_description',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function featureType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'feature_type_id');
    }
}
