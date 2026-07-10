<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EconomicActivity extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['economic_activity_type_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('people');
    }

    protected $fillable = [
        'id',
        'code',
        'description',
        'economic_activity_type_id',
        'is_principal',
        'fiscal_profile_id',
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

    /**
     * Relación con el perfil fiscal
     */
    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }

    /**
     * Relación con lookup (tipo de actividad económica)
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'economic_activity_type_id');
    }
}
