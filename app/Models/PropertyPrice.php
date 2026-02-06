<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPrice extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'property_id',
        'price_type_id',
        'price_min',
        'price_max',
        'price',
        'currency',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function priceType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'price_type_id');
    }
}

