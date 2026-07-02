<?php

namespace App\Models;

use App\Models\Concerns\TransformsTextCase;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasUuids, LogsActivity, SoftDeletes, TransformsTextCase;

    protected array $transformTextCase = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        $logName = match (true) {
            ! empty($this->company_id) => 'companies',
            ! empty($this->property_id) => 'properties',
            default => 'people',
        };

        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'mobile', 'email', 'is_principal', 'person_id', 'company_id', 'property_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($logName);
    }

    protected $fillable = [
        'name',
        'phone',
        'mobile',
        'email',
        'is_principal',
        'person_id',
        'company_id',
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

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
