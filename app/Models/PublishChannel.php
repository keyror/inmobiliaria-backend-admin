<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PublishChannel extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

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
        'company_id',
        'person_id',
        'property_id',
        'channel_id',
        'external_link',
        'status_id',
        'published_at',
        'unpublished_at',
        'channel_specific_data',
    ];

    protected $casts = [
        'channel_specific_data' => 'array',
        'published_at' => 'date:Y-m-d',
        'unpublished_at' => 'date:Y-m-d',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'channel_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
