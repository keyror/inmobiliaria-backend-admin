<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Property extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'code',
        'is_active',
        'status_id',
        'title',
        'offer_type_id',
        'property_type_id',
        'social_strata',
        'year_built',
        'rooms',
        'bedrooms',
        'bathrooms',
        'garages',
        'garage_type_id',
        'parking_spots',
        'parking_type_id',
        'cadastral_number',
        'url_google_map',
        'latitude',
        'longitude',
        'boundaries',
        'description',
    ];

    /** Relaciones con lookups */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function offerType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'offer_type_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'property_type_id');
    }

    public function garageType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'garage_type_id');
    }

    public function parkingType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'parking_type_id');
    }

    public function areas(): HasMany {
        return $this->hasMany(PropertyArea::class);
    }

    public function prices(): HasMany {
        return $this->hasMany(PropertyPrice::class);
    }

    public function publishChannels(): HasMany {
        return $this->hasMany(PropertyPublishChannel::class);
    }

    public function features(): HasMany {
        return $this->hasMany(PropertyFeature::class);
    }

    public function obligations(): HasMany {
        return $this->hasMany(PropertyObligation::class);
    }

    public function ownerships()
    {
        return $this->hasMany(PropertyPerson::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(
            Person::class,
            'property_person'
        )
            ->withPivot([
                'ownership_percentage',
                'is_primary_owner',
                'ownership_start_date',
                'ownership_end_date'
            ])
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }

}
