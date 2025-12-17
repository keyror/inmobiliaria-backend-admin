<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxeType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'code',
        'description',
        'taxe_type_id',
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
    public function fiscalProfiles(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'taxe_type_id');
    }
}
