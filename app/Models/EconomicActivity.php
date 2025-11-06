<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicActivity extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'code',
        'description',
        'economic_activity_type_id',
        'is_principal',
        'fiscal_profile_id',
    ];

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
