<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Property extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'code',
        'status_property_id',
        'status_id',
        'title',
        'offer_type_id',
        'property_type_id',
        'social_strata',
        'year_built',
        'rooms',
        'bedrooms',
        'bathrooms',
        'garage_type_id',
        'garage_spots',
        'cadastral_number',
        'url_google_map',
        'latitude',
        'longitude',
        'boundaries',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'created_at' => 'date:Y-m-d H:i:s',
        ];
    }

    /** Relaciones con lookups */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function statusProperty(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_property_id');
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

    public function areas(): HasMany {
        return $this->hasMany(PropertyArea::class);
    }

    public function price(): HasOne {
        return $this->hasOne(PropertyPrice::class);
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

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
            ->orderBy('sort_order');
    }

    public function contacts(): HasMany
    {
        return $this->HasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->HasMany(Address::class);
    }

    public function syncHasMany(
        string $relation,
        array $items,
        string $foreignKey = 'property_id',
        ?string $compositeKey = null
    ): void
    {
        if ($compositeKey) {
            // MODO CLAVE COMPUESTA (ownerships)
            $incomingKeys = collect($items)
                ->pluck($compositeKey)
                ->filter()
                ->values();

            // Soft delete de los que ya no vienen
            $this->$relation()
                ->whereNotIn($compositeKey, $incomingKeys)
                ->delete();

            foreach ($items as $item) {

                $item[$foreignKey] = $this->id;

                $existing = $this->$relation()
                    ->withTrashed()
                    ->where($foreignKey, $this->id)
                    ->where($compositeKey, $item[$compositeKey])
                    ->first();

                if ($existing) {
                    $existing->restore();
                    $existing->update($item);
                } else {
                    $this->$relation()->create($item);
                }
            }

        } else {

            // MODO NORMAL POR ID
            $ids = collect($items)->pluck('id')->filter();

            $this->$relation()
                ->whereNotIn('id', $ids)
                ->delete();

            foreach ($items as $item) {

                $item[$foreignKey] = $this->id;

                $this->$relation()->updateOrCreate(
                    ['id' => $item['id'] ?? null],
                    $item
                );
            }
        }
    }

    public function syncHasOne(
        string $relation,
        ?array $item,
        string $foreignKey = 'property_id'
    ): void
    {
        if (!$item) {
            $this->$relation()->delete();
            return;
        }

        $item[$foreignKey] = $this->id;

        $this->$relation()->updateOrCreate(
            [$foreignKey => $this->id],
            $item
        );
    }

}
