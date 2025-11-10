<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalProfile extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'tax_regime',
        'responsible_for_vat_type_id',
        'vat_withholding',
        'income_tax_withholding',
        'ica_withholding',
        'taxe_type_id'
    ];

    public function persons(): HasMany
    {
        return $this->HasMany(Person::class);
    }

    public function companies(): HasMany
    {
        return $this->HasMany(Company::class);
    }

    public function taxeType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'taxe_type_id');
    }

    public function vatType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'responsible_for_vat_type_id');
    }

    public function economicActivities()
    {
        return $this->hasMany(EconomicActivity::class, 'fiscal_profile_id');
    }

}
