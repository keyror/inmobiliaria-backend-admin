<?php

namespace Database\Seeders;

use App\Models\AccountBank;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EconomicActivity;
use App\Models\FiscalProfile;
use App\Models\Image;
use App\Models\Person;
use App\Models\Property;
use App\Models\PropertyArea;
use App\Models\PropertyFeature;
use App\Models\PropertyObligation;
use App\Models\PropertyPerson;
use App\Models\PropertyPrice;
use App\Models\PropertyPublishChannel;
use App\Models\TaxeType;
use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use App\Support\CalculateDV;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Random\RandomException;

class UsersTableSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $propertyImageFiles = [
            '14948bff-3f15-44a3-be8f-b1975b5be93b.webp',
            '1b05290c-02b5-46ff-a712-67566188504e.webp',
            '441bcbcc-b922-4e53-8443-6ad49c59e505.webp',
            '4b0d2503-5e49-4b4d-8a02-1ea7822cd97a.webp',
            'e0a455b4-d9e9-4705-8a68-8ec8032076c6.webp',
            'e529aef5-b7a9-41fe-90b9-38aeb4f68399.webp',
        ];

        // Obtenemos los lookups de las categorías necesarias
        $lookups = $lookupRepo->getLookupsByCategory([
            'taxe_type',
            'organization_type',
            'document_type',
            'status',
            'gender',
            'op_si_no',
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
            'frequency',
        ]);

        $usersData = [
            ['email' => 'camilomancipe@outlook.com', 'password' => '123456789a'],
            ['email' => 'jhon.doe@example.com', 'password' => '123456789a'],
            ['email' => 'maria.perez@example.com', 'password' => '123456789a'],
        ];

        foreach ($usersData as $userIndex => $data) {

            // Obtener ids de lookups de forma segura
            $taxeTypeId = $lookups->get('taxe_type')?->first() ?? null;
            $organizationTypeId = $lookups->get('organization_type')?->first()?->id ?? null;
            $documentTypeId = $lookups->get('document_type')?->first()?->id ?? null;
            $genderTypeId = $lookups->get('gender')?->first()?->id ?? null;
            $userStatusTypeId = $lookups->get('status')?->first()?->id ?? null;
            $vatTypeId = $lookups->get('op_si_no')?->first()?->id ?? null;
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
                'status_type_id' => $userStatusTypeId,
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
                'code' => $economicActiviy->code,
            ]);

            // Crear persona
            $parts = explode('@', $data['email']);
            $firstName = ucfirst($parts[0]);
            $lastName = 'Apellido';
            $fullName = $firstName.' '.$lastName;

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
                'person_id' => $person->id,
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
                'account_type_id' => $accountBanks,
            ]);

            for ($propertyIndex = 1; $propertyIndex <= 10; $propertyIndex++) {
                $propertySequence = ($userIndex + 1) * 1000 + $propertyIndex;
                $propertyCode = 'ABC'.$propertySequence;

                $property = Property::create([
                    'code' => $propertyCode,
                    'status_id' => $userStatusTypeId,
                    'status_property_id' => $propertyStatusTypeId,
                    'title' => 'Propiedad de prueba '.$propertyCode,
                    'offer_type_id' => $offerTypeId,
                    'property_type_id' => $propertyTypeId,
                    'social_strata' => (string) random_int(1, 6),
                    'year_built' => (string) random_int(1995, 2025),
                    'rooms' => (string) random_int(2, 8),
                    'bathrooms' => (string) random_int(1, 5),
                    'bedrooms' => (string) random_int(1, 5),
                    'garage_type_id' => $garageTypeId,
                    'garage_spots' => (string) random_int(0, 4),
                    'cadastral_number' => '000'.$propertySequence,
                    'url_google_map' => 'https://www.google.com/maps',
                    'latitude' => 5.30 + ($propertyIndex / 100),
                    'longitude' => -72.40 - ($propertyIndex / 100),
                    'boundaries' => 'Norte: via principal. Sur: zona residencial. Oriente: parque. Occidente: comercio.',
                    'description' => 'Propiedad de prueba creada por el seeder para validar el flujo inmobiliario.',
                ]);

                PropertyArea::create([
                    'property_id' => $property->id,
                    'area_type_id' => $areaTypeId,
                    'area_value' => random_int(20, 350),
                    'area_unit_id' => $areaUnitId,
                ]);

                $priceMin = random_int(50, 300) * 1000000;

                PropertyPrice::create([
                    'property_id' => $property->id,
                    'price_type_id' => $priceType,
                    'price_min' => $priceMin,
                    'price_max' => $priceMin + random_int(10, 200) * 1000000,
                    'price' => random_int(500000, 50000000),
                ]);

                PropertyPublishChannel::create([
                    'property_id' => $property->id,
                    'channel_id' => $channerlId,
                    'external_link' => 'https://www.youtube.com/watch?v=Sz_1tkcU0Co',
                    'published_at' => now(),
                    'unpublished_at' => now(),
                    'status_id' => $userStatusTypeId,
                ]);

                foreach ($propertyImageFiles as $index => $propertyImageFile) {
                    $imagePath = 'images/'.$propertyImageFile;
                    $absoluteImagePath = storage_path('app/public/'.$imagePath);

                    Image::create([
                        'imageable_id' => $property->id,
                        'imageable_type' => Property::class,
                        'title' => 'Imagen '.($index + 1).' de '.$property->code,
                        'description' => 'Imagen de prueba para la propiedad '.$property->code,
                        'file_name' => $propertyImageFile,
                        'file_path' => $imagePath,
                        'file_extension' => 'webp',
                        'mime_type' => 'image/webp',
                        'file_size' => file_exists($absoluteImagePath) ? filesize($absoluteImagePath) : 0,
                        'width' => 1200,
                        'height' => 800,
                        'sort_order' => $index,
                        'is_cover' => $index === 0,
                        'is_public' => true,
                    ]);
                }

                PropertyFeature::create([
                    'property_id' => $property->id,
                    'feature_type_id' => $featureId,
                    'feature_description' => 'Remodelación de toda la casa',
                ]);

                PropertyObligation::create([
                    'property_id' => $property->id,
                    'obligation_type_id' => $obligationId,
                    'amount' => random_int(500000, 5000000),
                    'total' => random_int(5000000, 50000000),
                    'frequency_type_id' => $frequencyId,
                    'expiration_date' => now(),
                    'description' => 'Mantenimiento de aire.',
                    'status_id' => $userStatusTypeId,
                ]);

                PropertyPerson::create([
                    'property_id' => $property->id,
                    'person_id' => $person->id,
                    'is_principal_owner' => true,
                    'status_id' => $userStatusTypeId,
                    'ownership_start_date' => now(),
                ]);

                Contact::create([
                    'phone' => '12345678',
                    'mobile' => '123456789',
                    'email' => $data['email'],
                    'is_principal' => true,
                    'property_id' => $property->id,
                ]);

                Address::create([
                    'property_id' => $property->id,
                    'via_type_id' => $viaTypeId,
                    'via_number' => (string) random_int(1, 99),
                    'letra1_id' => $letra1Id,
                    'orientation1_id' => $orientation1Id,
                    'number2' => (string) random_int(1, 99),
                    'letra2_id' => $letra2Id,
                    'orientation2_id' => $orientation2Id,
                    'number3' => (string) random_int(1, 99),
                    'address' => 'Calle '.$propertyIndex.' # '.random_int(1, 99).'-'.random_int(1, 99),
                    'city_id' => $city,
                    'department_id' => $department,
                    'country_id' => $country,
                    'stratum_id' => $stratum,
                    'zip_code' => '8500001',
                    'sector' => 'Sector '.$propertyIndex,
                    'complement' => 'Torre '.$propertyIndex,
                    'is_principal' => true,
                ]);
            }
        }
    }
}
