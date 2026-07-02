<?php

namespace App\Models;

use App\Models\Concerns\TransformsTextCase;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Address extends Model
{
    use HasUuids, LogsActivity, SoftDeletes, TransformsTextCase;

    protected array $transformTextCase = ['name', 'sector', 'complement'];

    public function getActivitylogOptions(): LogOptions
    {
        $logName = match (true) {
            ! empty($this->company_id) => 'companies',
            ! empty($this->property_id) => 'properties',
            default => 'people',
        };

        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($logName);
    }

    protected $fillable = [
        'name',
        'address',
        'city_id',
        'department_id',
        'country_id',
        'zip_code',
        'sector',
        'stratum_id',
        'complement',
        'via_type_id',
        'via_number',
        'letra1_id',
        'orientation1_id',
        'number2',
        'letra2_id',
        'orientation2_id',
        'number3',
        'person_id',
        'company_id',
        'is_principal',
        'property_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_principal' => 'bool',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'city_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'department_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'country_id');
    }

    public function stratum(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'stratum_id');
    }

    public function viaType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'via_type_id');
    }

    public function letra1(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'letra1_id');
    }

    public function orientation1(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'orientation1_id');
    }

    public function letra2(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'letra2_id');
    }

    public function orientation2(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'orientation2_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
