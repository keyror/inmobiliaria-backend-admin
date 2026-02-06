<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PropertyPerson extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'property_person';

    protected $fillable = [
        'property_id',
        'person_id',
        'ownership_percentage',
        'is_primary_owner',
        'ownership_start_date',
        'ownership_end_date',
    ];

    protected $casts = [
        'ownership_percentage'   => 'decimal:2',
        'is_primary_owner'       => 'boolean',
        'ownership_start_date'   => 'date',
        'ownership_end_date'     => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

