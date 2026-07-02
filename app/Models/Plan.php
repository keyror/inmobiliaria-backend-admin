<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Plan extends Model
{
    use HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('plans');
    }

    protected $table = 'plans';

    protected $connection = 'mysql';

    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'frequency_type_id',
        'discount',
        'max_users',
        'max_properties',
        'max_images_per_property',
        'is_active',
        'data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'integer',
        'max_users' => 'integer',
        'max_properties' => 'integer',
        'max_images_per_property' => 'integer',
        'is_active' => 'boolean',
        'data' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'frequency_type_id');
    }
}
