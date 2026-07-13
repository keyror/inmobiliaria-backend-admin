<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rent extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('rents');
    }

    protected $fillable = [
        'property_id',
        'status',
        'contract_number',
        'contract_type_id',
        'start_date',
        'end_date',
        'duration',
        'destination',
        'activity',
        'period',
        'canon',
        'iva',
        'administration_included',
        'is_ph',
        'interest_rate',
        'increment_type_id',
        'adjustment_date',
        'is_insured',
        'consignment_account',
        'payment_bank_id',
        'commissions',
        'signed_city',
        'signed_at',
        'additional_clauses',
        'internal_notes',
        'limit_dates_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'period' => 'date:Y-m-d',
            'adjustment_date' => 'date:Y-m-d',
            'signed_at' => 'date:Y-m-d',
            'canon' => 'decimal:2',
            'iva' => 'decimal:2',
            'administration_included' => 'boolean',
            'is_ph' => 'boolean',
            'is_insured' => 'boolean',
            'additional_clauses' => 'array',
            'created_at' => 'date:Y-m-d H:i:s',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'contract_type_id');
    }

    public function incrementType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'increment_type_id');
    }

    public function paymentBank(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'payment_bank_id');
    }

    public function limitDate(): BelongsTo
    {
        return $this->belongsTo(LimitDate::class, 'limit_dates_id');
    }

    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'rent_tenant_codebtor', 'rent_id', 'tenant_id')
            ->withPivot('codebtor_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    public function codebtors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'rent_tenant_codebtor', 'rent_id', 'codebtor_id')
            ->withPivot('tenant_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    public function rentTenantCodebtors(): HasMany
    {
        return $this->hasMany(RentTenantCodebtor::class);
    }

    public function liabilities(): HasMany
    {
        return $this->hasMany(Liability::class);
    }

    public function rentObligations(): HasMany
    {
        return $this->hasMany(RentObligation::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
