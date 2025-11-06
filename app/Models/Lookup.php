<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lookup extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'category',
        'value',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function peopleWithThisDocumentType(): HasMany
    {
        return $this->hasMany(Person::class, 'document_type_id');
    }

    public function peopleWithThisOrganizationType(): HasMany
    {
        return $this->hasMany(Person::class, 'organization_type_id');
    }

    public function peopleWithGenderType(): HasMany
    {
        return $this->hasMany(Person::class, 'gender_type_id');
    }

    public function usersWithStatusType(): HasMany
    {
        return $this->hasMany(User::class, 'status_type_id');
    }

    public function fiscalProfilesWithStatusType(): HasMany
    {
        return $this->hasMany(FiscalProfile::class, 'taxe_type_id');
    }

    public function fiscalProfilesWithVatType(): HasMany
    {
        return $this->hasMany(FiscalProfile::class, 'responsible_for_vat_type_id');
    }

    public function economicActivities()
    {
        return $this->hasMany(EconomicActivity::class, 'economic_activity_type_id');
    }

}
