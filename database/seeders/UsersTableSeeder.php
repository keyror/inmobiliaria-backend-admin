<?php

namespace Database\Seeders;

use App\Models\AccountBank;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EconomicActivity;
use App\Models\Property;
use App\Models\PropertyArea;
use App\Models\PropertyFeature;
use App\Models\PropertyObligation;
use App\Models\PropertyPerson;
use App\Models\PropertyPrice;
use App\Models\PropertyPublishChannel;
use App\Models\TaxeType;
use App\Models\User;
use App\Models\Person;
use App\Models\FiscalProfile;
use App\Repositories\Implements\LookupRepository;
use App\Support\CalculateDV;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository();

        // Obtenemos los lookups de las categorías necesarias
        $lookups = $lookupRepo->getLookupsByCategory([
            'taxe_type',
            'organization_type',
            'document_type',
            'user_status',
            'gender',
            'vat_type',
            'economic_activity',
            'city',
            'banks',
            'account_banks',
            'stratum',
            'department',
            'country',
            'road_type',
            'letter',
            'orientation',
            'garage_type',
            'property_type',
            'property_status',
            'offer_type',
            'area_type',
            'area_unit',
            'price_type',
            'publish_channel',
            'feature',
            'obligation_type',
            'frequency'
        ]);

        $usersData = [
            ['email' => 'camilomancipe@outlook.com', 'password' => '123456789'],
            ['email' => 'jhon.doe@example.com', 'password' => '123456789'],
            ['email' => 'maria.perez@example.com', 'password' => '123456789'],
        ];

        foreach ($usersData as $data) {

            // Obtener ids de lookups de forma segura
            $taxeTypeId = $lookups->get('taxe_type')?->first() ?? null;
            $organizationTypeId = $lookups->get('organization_type')?->first()?->id ?? null;
            $documentTypeId = $lookups->get('document_type')?->first()?->id ?? null;
            $genderTypeId = $lookups->get('gender')?->first()?->id ?? null;
            $userStatusTypeId = $lookups->get('user_status')?->first()?->id ?? null;
            $vatTypeId = $lookups->get('vat_type')?->first()?->id ?? null;
            $economicActiviy = $lookups->get('economic_activity')?->first() ?? null;
            $accountBanks = $lookups->get('account_banks')?->first()->id ?? null;
            $banks = $lookups->get('banks')?->first()->id ?? null;
            $stratum = $lookups->get('stratum')?->first()->id ?? null;
            $country = $lookups->get('country')?->first()->id ?? null;
            $department = $lookups->get('department')?->first()->id ?? null;
            $city = $lookups->get('city')?->first()->id ?? null;
            $viaTypeId = $lookups->get('road_type')?->first()?->id;
            $letra1Id = $lookups->get('letter')?->first()?->id;
            $orientation1Id = $lookups->get('orientation')?->first()?->id;
            $letra2Id = $lookups->get('letter')?->skip(1)->first()?->id;
            $orientation2Id = $lookups->get('orientation')?->skip(1)->first()?->id;
            $garageTypeId = $lookups->get('garage_type')?->first()?->id;
            $propertyStatusTypeId = $lookups->get('property_status')?->first()?->id ?? null;
            $offerTypeId = $lookups->get('offer_type')?->first()?->id ?? null;
            $propertyTypeId = $lookups->get('property_type')?->first()?->id ?? null;
            $areaTypeId = $lookups->get('area_type')?->first()?->id ?? null;
            $areaUnitId = $lookups->get('area_unit')?->first()?->id ?? null;
            $priceType = $lookups->get('price_type')?->first()?->id ?? null;
            $channerlId = $lookups->get('publish_channel')?->first()?->id ?? null;
            $featureId = $lookups->get('feature')?->first()?->id ?? null;
            $obligationId = $lookups->get('obligation_type')?->first()?->id ?? null;
            $frequencyId = $lookups->get('frequency')?->first()?->id ?? null;

            // Crear usuario
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status_type_id' => $userStatusTypeId
            ]);

            $user->assignRole('Admin');

            // Crear fiscal profile
            $fiscalProfile = FiscalProfile::create([
                'id' => Str::uuid(),
                'responsible_for_vat_type_id' => $vatTypeId,
                'vat_withholding' => 0.00,
                'income_tax_withholding' => 0.00,
                'ica_withholding' => 0.00,
            ]);

            TaxeType::create([
                'taxe_type_id' => $taxeTypeId->id,
                'code' => $taxeTypeId->code,
                'is_principal' => true,
                'fiscal_profile_id' => $fiscalProfile->id,
            ]);

            EconomicActivity::create([
                'economic_activity_type_id' => $economicActiviy->id,
                'fiscal_profile_id' => $fiscalProfile->id,
                'is_principal' => true,
                'code' => $economicActiviy->code
            ]);

            // Crear persona
            $parts = explode('@', $data['email']);
            $firstName = ucfirst($parts[0]);
            $lastName = 'Apellido';
            $fullName = $firstName . ' ' . $lastName;

            $document = rand(1000, 9999);
            $dv = CalculateDV::fromNumber($document);

            $person = Person::create([
                'user_id' => $user->id,
                'fiscal_profile_id' => $fiscalProfile->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $fullName,
                'company_name' => null,
                'document_number' => $document,
                'dv' => $dv,
                'document_from_id' => $city,
                'organization_type_id' => $organizationTypeId,
                'document_type_id' => $documentTypeId,
                'gender_type_id' => $genderTypeId,
                'birth_date' => now()->subYears(25),
            ]);

            Contact::create([
                'phone' => '12345678',
                'mobile' => '123456789',
                'email' => $data['email'],
                'is_principal' => true,
                'person_id' => $person->id
            ]);

            Address::create([
                'person_id' => $person->id,
                'via_type_id' => $viaTypeId,
                'via_number' => '22',
                'letra1_id' => $letra1Id,
                'orientation1_id' => $orientation1Id,
                'number2' => '22',
                'letra2_id' => $letra2Id,
                'orientation2_id' => $orientation2Id,
                'number3' => '33',
                'address' => 'Autopista 22 A Este # 22 B Noroccidente - 33',
                'city_id' => $city,
                'department_id' => $department,
                'country_id' => $country,
                'stratum_id' => $stratum,
                'zip_code' => '8500001',
                'sector' => 'Llano Vargas',
                'complement' => 'Torre 2',
                'is_principal' => true,
            ]);

            AccountBank::create([
                'person_id' => $person->id,
                'bank_id' => $banks,
                'account_number' => '123456789',
                'account_type_id' => $accountBanks
            ]);

            $property = Property::create([
                'code' => fake()->unique()->bothify('ABC####'),
                'status_id' => $propertyStatusTypeId,
                'title' => 'Casa N°1',
                'offer_type_id' => $offerTypeId,
                'property_type_id' => $propertyTypeId,
                'social_strata' => '3',
                'year_built' => '2023',
                'rooms' => '5',
                'bathrooms' => '2',
                'bedrooms' => '2',
                'garage_type_id' => $garageTypeId,
                'garage_spots' => '2',
                'cadastral_number' => fake()->unique()->bothify('000####'),
                'url_google_map' => 'www.google.com',
                'latitude' => 20,
                'longitude' => 20,
                'boundaries' => 'lorem20',
                'description' => 'Casa grande con garaje'
            ]);

            PropertyArea::create([
                'property_id' => $property->id,
                'area_type_id' => $areaTypeId,
                'area_value' => 20,
                'area_unit_id' => $areaUnitId
            ]);

            PropertyPrice::create([
               'property_id' => $property->id,
                'price_type_id' => $priceType,
                'price_min' => 100,
                'price_max' => 500,
                'price' => 250,
            ]);

            PropertyPublishChannel::create([
               'property_id' => $property->id,
                'channel_id' => $channerlId,
                'external_link' => 'www.google.com',
                'published_at' => now(),
                'unpublished_at' => now(),
                'channel_specific_data' => json_encode(['descrip'=> 'hola'])
            ]);

            PropertyFeature::create([
               'property_id' => $property->id,
               'feature_type_id' => $featureId,
               'feature_description' => 'Remodelación de toda la casa'
            ]);

            PropertyObligation::create([
               'property_id' => $property->id,
               'obligation_type_id' => $obligationId,
                'amount' => 1000,
                'total' => 12000,
                'frequency_type_id' => $frequencyId,
                'expiration_date' => now(),
                'description' => 'Mantenimiento de aire.'
            ]);

            PropertyPerson::create([
                'property_id' => $property->id,
                'person_id' => $person->id,
                'is_primary_owner' => true,
                'ownership_start_date' => now(),
            ]);
        }
    }
}
