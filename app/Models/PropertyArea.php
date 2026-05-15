<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyArea extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'property_id',
        'area_type_id',
        'area_value',
        'area_unit_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'area_value' => 'float',
        ];
    }

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
