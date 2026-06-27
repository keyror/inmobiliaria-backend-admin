<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Image;
use App\Models\Person;
use App\Models\Property;
use App\Models\PropertyArea;
use App\Models\PropertyFeature;
use App\Models\PropertyObligation;
use App\Models\PropertyPerson;
use App\Models\PropertyPrice;
use App\Models\PropertyPublishChannel;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Random\RandomException;

class PropertiesTableSeeder extends Seeder
{
    private array $imageFiles = [
        '14948bff-3f15-44a3-be8f-b1975b5be93b.webp',
        '1b05290c-02b5-46ff-a712-67566188504e.webp',
        '441bcbcc-b922-4e53-8443-6ad49c59e505.webp',
        '4b0d2503-5e49-4b4d-8a02-1ea7822cd97a.webp',
        'e0a455b4-d9e9-4705-8a68-8ec8032076c6.webp',
        'e529aef5-b7a9-41fe-90b9-38aeb4f68399.webp',
    ];

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory([
            'status',
            'city',
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

        $statusId = $lookups->get('status')?->first()?->id;
        $cityId = $lookups->get('city')?->first()?->id;
        $departmentId = $lookups->get('department')?->first()?->id;
        $countryId = $lookups->get('country')?->first()?->id;
        $viaTypeId = $lookups->get('road_type')?->first()?->id;
        $letra1Id = $lookups->get('letter')?->first()?->id;
        $orientation1Id = $lookups->get('orientation')?->first()?->id;
        $letra2Id = $lookups->get('letter')?->skip(1)->first()?->id;
        $orientation2Id = $lookups->get('orientation')?->skip(1)->first()?->id;
        $stratumId = $lookups->get('stratum')?->first()?->id;
        $garageTypeId = $lookups->get('garage_type')?->first()?->id;
        $propertyTypeId = $lookups->get('property_type')?->first()?->id;
        $propertyStatusId = $lookups->get('property_status')?->first()?->id;
        $areaTypeId = $lookups->get('area_type')?->first()?->id;
        $areaUnitId = $lookups->get('area_unit')?->first()?->id;
        $channelId = $lookups->get('publish_channel')?->first()?->id;
        $featureId = $lookups->get('feature')?->first()?->id;
        $obligationId = $lookups->get('obligation_type')?->first()?->id;
        $frequencyId = $lookups->get('frequency')?->first()?->id;
        $offerTypes = $lookups->get('offer_type') ?? collect();
        $priceTypes = $lookups->get('price_type') ?? collect();

        foreach (Person::all() as $userIndex => $person) {
            for ($i = 1; $i <= 10; $i++) {
                $sequence = ($userIndex + 1) * 1000 + $i;
                $code = 'ABC'.$sequence;
                $offerType = $offerTypes->values()->get(($i - 1) % $offerTypes->count());

                $property = Property::create([
                    'code' => $code,
                    'status_id' => $statusId,
                    'status_property_id' => $propertyStatusId,
                    'title' => 'Propiedad de prueba '.$code,
                    'offer_type_id' => $offerType?->id,
                    'property_type_id' => $propertyTypeId,
                    'social_strata' => (string) random_int(1, 6),
                    'year_built' => (string) random_int(1995, 2025),
                    'rooms' => (string) random_int(2, 8),
                    'bathrooms' => (string) random_int(1, 5),
                    'bedrooms' => (string) random_int(1, 5),
                    'garage_type_id' => $garageTypeId,
                    'garage_spots' => (string) random_int(0, 4),
                    'cadastral_number' => '000'.$sequence,
                    'url_google_map' => 'https://www.google.com/maps',
                    'latitude' => 5.30 + ($i / 100),
                    'longitude' => -72.40 - ($i / 100),
                    'boundaries' => 'Norte: via principal. Sur: zona residencial. Oriente: parque. Occidente: comercio.',
                    'description' => 'Propiedad de prueba creada por el seeder para validar el flujo inmobiliario.',
                ]);

                PropertyArea::create([
                    'property_id' => $property->id,
                    'area_type_id' => $areaTypeId,
                    'area_value' => random_int(20, 350),
                    'area_unit_id' => $areaUnitId,
                ]);

                $this->createPrices($property, $offerType, $priceTypes);

                PropertyPublishChannel::create([
                    'property_id' => $property->id,
                    'channel_id' => $channelId,
                    'external_link' => 'https://www.youtube.com/watch?v=Sz_1tkcU0Co',
                    'published_at' => now(),
                    'unpublished_at' => now(),
                    'status_id' => $statusId,
                ]);

                $this->createImages($property);

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
                    'status_id' => $statusId,
                ]);

                PropertyPerson::create([
                    'property_id' => $property->id,
                    'person_id' => $person->id,
                    'is_principal_owner' => true,
                    'status_id' => $statusId,
                    'ownership_start_date' => now(),
                ]);

                Contact::create([
                    'phone' => '12345678',
                    'mobile' => '123456789',
                    'email' => $person->user->email,
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
                    'address' => 'Calle '.$i.' # '.random_int(1, 99).'-'.random_int(1, 99),
                    'city_id' => $cityId,
                    'department_id' => $departmentId,
                    'country_id' => $countryId,
                    'stratum_id' => $stratumId,
                    'zip_code' => '8500001',
                    'sector' => 'Sector '.$i,
                    'complement' => 'Torre '.$i,
                    'is_principal' => true,
                ]);
            }
        }
    }

    private function createPrices(Property $property, $offerType, $priceTypes): void
    {
        $aliases = $offerType?->code
            ? array_map('trim', explode(',', $offerType->code))
            : [];

        foreach ($aliases as $alias) {
            $priceTypeId = $priceTypes->firstWhere('alias', $alias)?->id;
            if (! $priceTypeId) {
                continue;
            }

            $isArriendo = str_contains($alias, 'ARRIENDO');
            $priceMin = $isArriendo ? random_int(1, 5) * 1_000_000 : random_int(100, 500) * 1_000_000;
            $priceMax = $isArriendo ? $priceMin + random_int(500_000, 2_000_000) : $priceMin + random_int(10, 200) * 1_000_000;
            $price = $isArriendo ? $priceMin + random_int(100_000, 500_000) : $priceMin + random_int(1, 9) * 1_000_000;

            PropertyPrice::create([
                'property_id' => $property->id,
                'price_type_id' => $priceTypeId,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
                'price' => $price,
                'currency' => 'COP',
            ]);
        }
    }

    private function createImages(Property $property): void
    {
        foreach ($this->imageFiles as $index => $fileName) {
            $filePath = 'images/'.$fileName;
            $absolutePath = storage_path('app/public/'.$filePath);

            Image::create([
                'imageable_id' => $property->id,
                'imageable_type' => Property::class,
                'title' => 'Imagen '.($index + 1).' de '.$property->code,
                'description' => 'Imagen de prueba para la propiedad '.$property->code,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_extension' => 'webp',
                'mime_type' => 'image/webp',
                'file_size' => file_exists($absolutePath) ? filesize($absolutePath) : 0,
                'width' => 1200,
                'height' => 800,
                'sort_order' => $index,
                'is_cover' => $index === 0,
                'is_public' => true,
            ]);
        }
    }
}
