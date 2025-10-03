<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rent extends Model
{
    use HasUuids, SoftDeletes;

    /**
     * Método para obtener lo arrendatarios.
     *
     * @return BelongsToMany
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'rent_tenant_codebtor', 'rent_id', 'tenant_id')
            ->withPivot('codebtor_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    /**
     * Método para obtener los codeudores.
     *
     * @return BelongsToMany
     */
    public function codebtors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'rent_tenant_codebtor', 'rent_id', 'codebtor_id')
            ->withPivot('tenant_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }


    /**
     * Método para obtener la tabla pivot
     *
     * @return HasMany
     */
    public function rentTenantCodebtors(): HasMany
    {
        return $this->hasMany(RentTenantCodebtor::class);
    }

    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }
}
