<?php

namespace App\Models;

use App\Support\CalculateDV;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Person extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'fiscal_profile_id',
        'first_name',
        'last_name',
        'full_name',
        'company_name',
        'document_type_id',
        'document_number',
        'dv',
        'document_from_id',
        'organization_type_id',
        'birth_date',
        'gender_type_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'date:Y-m-d H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentFrom(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'document_from_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'document_type_id');
    }

    public function genderType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'gender_type_id');
    }

    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'organization_type_id');
    }

    public function contacts(): HasMany
    {
        return $this->HasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->HasMany(Address::class);
    }

    public function accountBanks(): HasMany
    {
        return $this->HasMany(AccountBank::class);
    }

    // Relación cuando la persona actúa como tenant
    public function rentsAsTenant(): BelongsToMany
    {
        return $this->belongsToMany(
            Rent::class,
            'rent_tenant_codebtor',
            'tenant_id',
            'rent_id'
        )
            ->withPivot('codebtor_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    // Relación cuando la persona actúa como codebtor
    public function rentsAsCodebtor(): BelongsToMany
    {
        return $this->belongsToMany(
            Rent::class,
            'rent_tenant_codebtor',
            'codebtor_id',
            'rent_id'
        )
            ->withPivot('tenant_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    // Relación con personas que son codebtors cuando esta persona es tenant
    public function codebtorsWhenTenant(): BelongsToMany
    {
        return $this->belongsToMany(
            Person::class,
            'rent_tenant_codebtor',
            'tenant_id',
            'codebtor_id'
        )
            ->withPivot('rent_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    // Relación con personas que son tenants cuando esta persona es codebtor
    public function tenantsWhenCodebtor(): BelongsToMany
    {
        return $this->belongsToMany(
            Person::class,
            'rent_tenant_codebtor',
            'codebtor_id',
            'tenant_id'
        )
            ->withPivot('rent_id')
            ->withTimestamps()
            ->using(RentTenantCodebtor::class);
    }

    protected function documentNumber(): Attribute
    {
        return Attribute::set(function ($value) {
            // Asigna el número
            $this->attributes['document_number'] = $value;

            // Calcula y guarda el DV automáticamente
            $this->attributes['dv'] = $value
                ? CalculateDV::fromNumber($value)
                : '';

            return $value;
        });
    }

    public function syncHasMany(
        string $relation,
        array $items,
        string $foreignKey = 'person_id'
    ): void
    {
        $ids = collect($items)->pluck('id')->filter();

        $this->$relation()
            ->whereNotIn('id', $ids)
            ->delete();

        foreach ($items as $item) {
            $item[$foreignKey] = $this->id;

            $this->$relation()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                $item
            );
        }
    }

}
