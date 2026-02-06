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

    public function addressCities() {
        return $this->hasOne(Address::class, 'city_id');
    }

    public function addressDepartments() {
        return $this->hasOne(Address::class, 'department_id');
    }

    public function addressCountries() {
        return $this->hasOne(Address::class, 'country_id');
    }

    public function addressStratum() {
        return $this->hasOne(Address::class, 'stratum_id');
    }

    public function accountBankTypes() {
        return $this->hasOne(Address::class, 'account_type_id');
    }

    public function banks() {
        return $this->hasOne(Address::class, 'bank_id');
    }

    public function addressesByViaType(): HasMany
    {
        return $this->hasMany(Address::class, 'via_type_id');
    }

    public function addressesByLetra1(): HasMany
    {
        return $this->hasMany(Address::class, 'letra1_id');
    }

    public function addressesByOrientation1(): HasMany
    {
        return $this->hasMany(Address::class, 'orientation1_id');
    }

    public function addressesByLetra2(): HasMany
    {
        return $this->hasMany(Address::class, 'letra2_id');
    }

    public function addressesByOrientation2(): HasMany
    {
        return $this->hasMany(Address::class, 'orientation2_id');
    }

    public function propertiesByStatus()
    {
        return $this->hasMany(Property::class, 'status_id');
    }

    public function propertiesByOfferType()
    {
        return $this->hasMany(Property::class, 'offer_type_id');
    }

    public function propertiesByPropertyType()
    {
        return $this->hasMany(Property::class, 'property_type_id');
    }

    public function propertiesByGarageType()
    {
        return $this->hasMany(Property::class, 'garage_type_id');
    }

    public function propertiesByParkingType()
    {
        return $this->hasMany(Property::class, 'parking_type_id');
    }

    public function propertyAreas()
    {
        return $this->hasMany(PropertyArea::class, 'area_type_id');
    }

    public function propertyPrices()
    {
        return $this->hasMany(PropertyPrice::class, 'price_type_id');
    }

    public function publishChannels()
    {
        return $this->hasMany(PropertyPublishChannel::class, 'channel_id');
    }

    public function propertyFeatures()
    {
        return $this->hasMany(PropertyFeature::class, 'feature_type_id');
    }

    public function obligations()
    {
        return $this->hasMany(PropertyObligation::class, 'obligation_type_id');
    }

    public function obligationFrequencies()
    {
        return $this->hasMany(PropertyObligation::class, 'frequency_type_id');
    }

}
