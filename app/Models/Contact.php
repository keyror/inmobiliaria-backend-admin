<?php

namespace App\Models;

use App\Models\Concerns\TransformsTextCase;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasUuids, LogsActivity, SoftDeletes, TransformsTextCase;

    protected array $transformTextCase = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        $logName = match ($this->contactable_type) {
            Company::class => 'companies',
            Property::class => 'properties',
            default => 'people',
        };

        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'mobile', 'email', 'is_principal'])
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
        'contactable_type',
        'contactable_id',
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

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
