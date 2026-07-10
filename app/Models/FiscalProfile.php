<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FiscalProfile extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {

        return LogOptions::defaults()
            ->logOnly([
                'tax_regime',
                'responsible_for_vat_type_id',
                'vat_withholding',
                'income_tax_withholding',
                'ica_withholding',
                'rental_fee',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('people');
    }

    protected $fillable = [
        'tax_regime',
        'responsible_for_vat_type_id',
        'vat_withholding',
        'income_tax_withholding',
        'ica_withholding',
        'rental_fee',
    ];

    public function persons(): HasMany
    {
        return $this->HasMany(Person::class);
    }

    public function companies(): HasMany
    {
        return $this->HasMany(Company::class);
    }

    public function taxeTypes(): HasMany
    {
        return $this->HasMany(TaxeType::class);
    }

    public function vatType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'responsible_for_vat_type_id');
    }

    public function economicActivities()
    {
        return $this->hasMany(EconomicActivity::class, 'fiscal_profile_id');
    }

    public function syncHasMany(
        string $relationName,
        array $ids,
        string $foreignKey
    ): void {
        $relation = $this->{$relationName}();

        $existing = $relation->pluck('id', $foreignKey);

        $toDelete = $existing->keys()->diff($ids);
        if ($toDelete->isNotEmpty()) {
            $relation->whereIn($foreignKey, $toDelete->all())->delete();
        }

        $toCreate = collect($ids)->diff($existing->keys());
        foreach ($toCreate as $id) {
            $relation->create([
                $foreignKey => $id,
                'fiscal_profile_id' => $this->id,
            ]);
        }
    }
}
