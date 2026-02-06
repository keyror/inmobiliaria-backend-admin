<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyObligation extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'property_id',
        'obligation_type_id',
        'amount',
        'total',
        'frequency_type_id',
        'expiration_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expiration_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function obligationType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'obligation_type_id');
    }

    public function frequencyType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'frequency_type_id');
    }
}

