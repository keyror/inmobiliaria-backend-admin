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
        'responsible_for_vat',
        'vat_withholding',
        'income_tax_withholding',
        'ica_withholding',
        'economic_activity',
        'dv',
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
}
