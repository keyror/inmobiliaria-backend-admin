<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lookup extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'category',
        'value',
        'code',
        'is_active',
        'lang'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function scopeCountries($query)
    {
        return $query->where('category', 'country');
    }

    public function scopeDepartments($query)
    {
        return $query->where('category', 'department');
    }

    public function scopeCities($query)
    {
        return $query->where('category', 'city');
    }

    public function peopleWithThisDocumentFrom(): HasOne
    {
        return $this->hasOne(Person::class, 'document_from_id');
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

    public function fiscalProfilesWithTaxeType(): HasMany
    {
        return $this->hasOne(TaxeType::class, 'taxe_type_id');
    }

    public function fiscalProfilesWithVatType(): HasMany
    {
        return $this->hasMany(FiscalProfile::class, 'responsible_for_vat_type_id');
    }

    public function economicActivities()
    {
        return $this->hasMany(EconomicActivity::class, 'economic_activity_type_id');
    }

    public function departments()
    {
        return $this->hasMany(self::class, 'code', 'code')
            ->where('category', 'department');
    }

    public function cities()
    {
        return $this->hasMany(self::class, 'code', 'alias')
            ->where('category', 'city');
    }

}
