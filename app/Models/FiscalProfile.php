<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalProfile extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'person_id',
        'company_id',
        'tax_regime',
        'responsible_for_vat',
        'economic_activity',
        'dv',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
