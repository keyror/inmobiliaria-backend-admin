<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyArea extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'property_id',
        'area_type_id',
        'area_value',
        'area_unit_id',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function areaType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'area_type_id');
    }

    public function areaUnit(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'area_unit_id');
    }
}

