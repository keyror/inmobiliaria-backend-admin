<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountBank extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        $logName = match ($this->accountable_type) {
            Company::class => 'companies',
            default => 'people',
        };

        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['accountable_type', 'accountable_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($logName);
    }

    protected $fillable = [
        'account_type_id',
        'bank_id',
        'account_number',
        'is_principal',
        'accountable_type',
        'accountable_id',
    ];

    protected function casts(): array
    {
        return [
            'is_principal' => 'bool',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'account_type_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'bank_id');
    }

    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }
}
