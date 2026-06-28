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
use App\Models\PublishChannel;
use App\Repositories\Implements\LookupRepository;
use Illuminate\Database\Seeder;
use Random\RandomException;

class PropertiesTableSeeder extends Seeder
{
    private const string PX = 'https://cdn.pixabay.com/photo/';

    private array $titles = [
        'Apartamento moderno con balcón y vista panorámica',
        'Casa familiar de dos pisos con jardín privado',
        'Apartaestudio amoblado ideal para profesionales',
        'Penthouse de lujo con terraza privada',
        'Apartamento en conjunto cerrado con zonas comunes',
        'Casa bifamiliar en sector residencial tranquilo',
        'Loft moderno en zona empresarial',
        'Apartamento familiar cerca a colegios y parques',
        'Casa esquinera con amplio garaje cubierto',
        'Apartamento de planta baja con patio interior',
        'Casa con acabados de lujo en sector exclusivo',
        'Apartamento dúplex con vista a la montaña',
        'Casa en conjunto con piscina y cancha',
        'Apartamento en edificio con piscina y gimnasio',
        'Casa campestre con zona de BBQ y jardín amplio',
    ];

    private array $descriptions = [
        'Hermoso apartamento con diseño contemporáneo, pisos en porcelanato importado, cocina integral con isla y amplias ventanas que permiten la entrada de luz natural. Conjunto residencial con vigilancia 24 horas, zonas verdes y parqueadero cubierto.',
        'Espaciosa casa de dos pisos en sector residencial tranquilo. Cuenta con sala-comedor amplio, cocina independiente, cuatro habitaciones con closets, tres baños completos y un patio interior con zona de lavandería. Garaje para dos vehículos.',
        'Cómodo apartaestudio totalmente amoblado y equipado, ideal para profesionales o estudiantes universitarios. Ubicado a pocos minutos de centros comerciales, universidades y estaciones del metro. Edificio con lobby, citófono y cámaras de seguridad.',
        'Exclusivo penthouse en edificio premium con acabados de primera. Terraza privada de 80 m² con vista a la ciudad, sala doble altura, cocina estilo europeo, cuarto de servicio, dos parqueaderos y depósito. Edificio con concierge.',
        'Apartamento en conjunto cerrado con excelente distribución de espacios. Tres habitaciones, dos baños, sala-comedor, balcón y cocina integral. El conjunto dispone de piscina, gimnasio, salón social y vigilancia las 24 horas.',
        'Amplia casa bifamiliar con independencia total en dos niveles. Cada piso cuenta con sala-comedor, dos habitaciones, baño completo y cocina. Ideal para familia extensa o como fuente de renta. Ubicada en calle sin salida, muy tranquila.',
        'Moderno loft en edificio de uso mixto, ideal para oficina o vivienda creativa. Planta abierta, techos altos, sistema de iluminación LED y acabados industriales. Localizado en zona empresarial con fácil acceso a vías principales.',
        'Acogedor apartamento familiar en sector residencial consolidado, a una cuadra del parque y a pocas calles de colegios bilingues y supermercados. Tres habitaciones amplias, cocina con comedor auxiliar, dos baños y depósito.',
        'Casa esquinera con amplio garaje para dos carros, cuarto de servicio con baño independiente, sala-comedor con chimenea, cocina equipada y patio con jardín. Ubicada en vía principal del barrio con fácil acceso a transporte público.',
        'Apartamento en primer piso con patio interior privado, ideal para mascotas y jardín propio. Sala-comedor luminoso, cocina abierta, dos habitaciones y dos baños. Conjunto pequeño y tranquilo con parqueadero disponible.',
        'Residencia de lujo con materiales importados, pisos en mármol, cocina Bosch, baños tipo spa y sistema de domótica. Cuatro habitaciones con vestier, zona de BBQ en terraza y garaje techado para cuatro vehículos.',
        'Apartamento dúplex con sala en doble altura y terraza privada con vista a la cordillera. Dos habitaciones en segundo nivel, una en primer nivel para visitas. Edificio boutique de apenas 12 unidades, excelente valorización.',
        'Casa en conjunto residencial cerrado con acceso controlado. Cuenta con piscina familiar, cancha de tenis, parque infantil y salón comunal. La casa tiene tres habitaciones, tres baños, cocina integral y patio trasero.',
        'Apartamento en edificio de alta gama con piscina olímpica, gimnasio equipado, salón de eventos y zona de BBQ. Tres habitaciones en suite, cocina abierta, sala grande con balcón y dos parqueaderos subterráneos.',
        'Hermosa casa campestre en las afueras de la ciudad con 2.500 m² de lote. Cuenta con zona de BBQ cubierta, jardín con huerto, casa principal de tres habitaciones y cabaña adicional para invitados. Vista panorámica a la montaña.',
    ];

    private array $sectors = [
        'Chapinero Alto',
        'El Poblado',
        'Barrio Chicó Navarra',
        'Los Rosales',
        'Quinta Camacho',
        'Santa Bárbara Norte',
        'Laureles',
        'Ciudad Jardín',
        'El Peñón',
        'La Castellana',
        'Cedritos',
        'Usaquén',
        'Barrio El Golf',
        'La Macarena',
        'Santa Ana Oriental',
    ];

    private array $complements = [
        'Apto 101',
        'Torre A Apto 502',
        'Piso 3 Apto 301',
        'Casa 15',
        'Unidad 204',
        'Torre 2 Apto 401',
        'Piso 12 Apto 1201',
        'Apto 203',
        'Torre B Piso 4',
        'Casa Interior',
        'Apto 302',
        'Penthouse',
        'Dúplex 501',
        'Apto 605',
        'Casa 7B',
    ];

    private array $features = [
        'Remodelación integral con acabados de primera',
        'Cocina equipada con electrodomésticos Whirlpool',
        'Sistema de paneles solares instalado',
        'Jacuzzi en baño principal',
        'Alarma perimetral y cámaras de seguridad',
        'Piso radiante en habitaciones principales',
        'Ventanas con doble vidrio termoacústico',
        'Chimenea eléctrica en sala',
        'Closets con madera lacada de alta calidad',
        'Terraza con jardín vertical',
        'Acceso para personas con movilidad reducida',
        'Punto de carga para vehículo eléctrico en garaje',
        'Bodega con sistema de climatización',
        'Sistema de automatización del hogar (domótica)',
        'Piscina privada con sistema de calefacción solar',
    ];

    private array $obligations = [
        'Administración mensual del conjunto residencial',
        'Impuesto predial vigente',
        'Mantenimiento de aires acondicionados',
        'Seguro de incendio y terremoto',
        'Cuota extraordinaria fondo de imprevistos',
        'Servicio de vigilancia privada',
        'Mantenimiento de ascensores',
        'Pago de servicios comunales (agua y gas)',
        'Cuota de parqueadero comunal',
        'Mantenimiento de zonas verdes',
    ];

    private array $exteriorImages = [
        '2014/07/10/17/18/large-home-389271_1280.jpg',
        '2014/10/05/22/39/real-estate-475875_1280.jpg',
        '2014/10/05/22/40/family-home-475879_1280.jpg',
        '2016/04/25/23/30/house-1353389_1280.jpg',
        '2017/04/10/22/28/residence-2219972_1280.jpg',
        '2021/01/09/15/30/house-5902664_1280.jpg',
        '2021/11/16/02/23/house-6799908_1280.jpg',
        '2014/07/16/20/43/building-394961_1280.jpg',
        '2018/01/26/08/37/architecture-3108075_1280.jpg',
        '2021/07/01/01/15/condominium-6377940_1280.jpg',
        '2021/12/05/15/37/building-6848037_1280.jpg',
        '2024/09/17/11/19/real-estate-9053405_1280.jpg',
    ];

    private array $livingRoomImages = [
        '2014/11/11/22/54/living-room-527646_1280.jpg',
        '2023/11/06/02/18/living-room-8368639_1280.jpg',
        '2024/07/05/08/19/living-room-8874204_1280.jpg',
        '2024/08/31/11/18/living-room-9011266_1280.jpg',
        '2024/07/15/11/54/apartment-8896495_1280.jpg',
    ];

    private array $kitchenImages = [
        '2017/06/13/22/42/kitchen-2400367_1280.jpg',
        '2023/11/06/02/20/kitchen-8368660_1280.jpg',
        '2024/07/05/08/21/kitchen-8874296_1280.jpg',
        '2024/08/31/11/18/kitchen-9011230_1280.jpg',
    ];

    private array $bedroomImages = [
        '2014/11/11/22/54/bedroom-527645_1280.jpg',
        '2024/06/22/13/31/bedroom-8846367_1280.jpg',
        '2024/07/05/08/21/bedroom-8874302_1280.jpg',
    ];

    private array $bathroomImages = [
        '2021/12/25/13/09/bathroom-6893066_1280.jpg',
        '2024/08/31/11/18/bathroom-9011240_1280.jpg',
    ];

    private array $diningImages = [
        '2024/08/31/11/18/dining-area-9011242_1280.jpg',
        '2024/08/31/11/18/dining-room-9011268_1280.jpg',
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
            'stratum',
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
        $areaTypeId = $lookups->get('area_type')?->first()?->id;
        $areaUnitId = $lookups->get('area_unit')?->first()?->id;
        $channelId = $lookups->get('publish_channel')?->first()?->id;
        $featureId = $lookups->get('feature')?->first()?->id;
        $obligationId = $lookups->get('obligation_type')?->first()?->id;
        $frequencyId = $lookups->get('frequency')?->first()?->id;
        $propertyTypes = $lookups->get('property_type') ?? collect();
        $propertyStatusId = $lookups->get('property_status')?->first()?->id;
        $offerTypes = $lookups->get('offer_type') ?? collect();
        $priceTypes = $lookups->get('price_type') ?? collect();

        $mobilePrefixes = ['300', '301', '302', '310', '311', '312', '313', '314', '315', '316', '317', '318', '319', '320', '321', '322'];

        $globalIndex = 0;

        foreach (Person::all() as $userIndex => $person) {
            for ($i = 1; $i <= 10; $i++) {
                $templateIdx = $globalIndex % count($this->titles);
                $sequence = ($userIndex + 1) * 1000 + $i;
                $code = 'VLT'.str_pad($sequence, 5, '0', STR_PAD_LEFT);
                $offerType = $offerTypes->values()->get(($globalIndex) % max($offerTypes->count(), 1));
                $propertyType = $propertyTypes->values()->get($globalIndex % max($propertyTypes->count(), 1));

                $viaNumber = random_int(10, 120);
                $crossNumber = random_int(10, 99);
                $doorNumber = random_int(10, 99);

                $property = Property::create([
                    'code' => $code,
                    'status_id' => $statusId,
                    'status_property_id' => $propertyStatusId,
                    'title' => $this->titles[$templateIdx],
                    'offer_type_id' => $offerType?->id,
                    'property_type_id' => $propertyType?->id ?? $propertyTypes->first()?->id,
                    'social_strata' => (string) random_int(3, 6),
                    'year_built' => (string) random_int(2000, 2024),
                    'rooms' => (string) random_int(3, 8),
                    'bathrooms' => (string) random_int(1, 4),
                    'bedrooms' => (string) random_int(2, 5),
                    'garage_type_id' => $garageTypeId,
                    'garage_spots' => (string) random_int(1, 3),
                    'cadastral_number' => 'BOG'.str_pad($sequence, 8, '0', STR_PAD_LEFT),
                    'url_google_map' => 'https://www.google.com/maps',
                    'latitude' => 4.60 + (random_int(0, 100) / 1000),
                    'longitude' => -74.08 - (random_int(0, 100) / 1000),
                    'boundaries' => 'Norte: vía principal. Sur: zona residencial. Oriente: parque. Occidente: comercio local.',
                    'description' => $this->descriptions[$templateIdx],
                ]);

                PropertyArea::create([
                    'property_id' => $property->id,
                    'area_type_id' => $areaTypeId,
                    'area_value' => random_int(45, 280),
                    'area_unit_id' => $areaUnitId,
                ]);

                $this->createPrices($property, $offerType, $priceTypes);

                PublishChannel::create([
                    'property_id' => $property->id,
                    'channel_id' => $channelId,
                    'external_link' => 'https://www.metrocuadrado.com',
                    'published_at' => now()->subDays(random_int(1, 60)),
                    'status_id' => $statusId,
                ]);

                $this->createImages($property, $globalIndex);

                PropertyFeature::create([
                    'property_id' => $property->id,
                    'feature_type_id' => $featureId,
                    'feature_description' => $this->features[$globalIndex % count($this->features)],
                ]);

                PropertyObligation::create([
                    'property_id' => $property->id,
                    'obligation_type_id' => $obligationId,
                    'amount' => random_int(3, 12) * 100_000,
                    'total' => random_int(1, 5) * 1_000_000,
                    'frequency_type_id' => $frequencyId,
                    'expiration_date' => now()->addMonths(random_int(1, 12)),
                    'description' => $this->obligations[$globalIndex % count($this->obligations)],
                    'status_id' => $statusId,
                ]);

                PropertyPerson::create([
                    'property_id' => $property->id,
                    'person_id' => $person->id,
                    'is_principal_owner' => true,
                    'status_id' => $statusId,
                    'ownership_start_date' => now()->subYears(random_int(1, 10)),
                ]);

                $mobilePrefix = $mobilePrefixes[$globalIndex % count($mobilePrefixes)];
                Contact::create([
                    'phone' => '60'.random_int(1000000, 9999999),
                    'mobile' => $mobilePrefix.random_int(1000000, 9999999),
                    'email' => $person->user->email,
                    'is_principal' => true,
                    'property_id' => $property->id,
                ]);

                $sector = $this->sectors[$templateIdx];
                Address::create([
                    'property_id' => $property->id,
                    'via_type_id' => $viaTypeId,
                    'via_number' => (string) $viaNumber,
                    'letra1_id' => $letra1Id,
                    'orientation1_id' => $orientation1Id,
                    'number2' => (string) $crossNumber,
                    'letra2_id' => $letra2Id,
                    'orientation2_id' => $orientation2Id,
                    'number3' => (string) $doorNumber,
                    'address' => "Calle {$viaNumber} # {$crossNumber} - {$doorNumber}",
                    'city_id' => $cityId,
                    'department_id' => $departmentId,
                    'country_id' => $countryId,
                    'stratum_id' => $stratumId,
                    'zip_code' => '110'.random_int(100, 999),
                    'sector' => $sector,
                    'complement' => $this->complements[$templateIdx],
                    'is_principal' => true,
                ]);

                $globalIndex++;
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
            $priceMin = $isArriendo ? random_int(1, 5) * 1_000_000 : random_int(100, 800) * 1_000_000;
            $priceMax = $isArriendo ? $priceMin + random_int(500_000, 2_000_000) : $priceMin + random_int(20, 300) * 1_000_000;
            $price = $isArriendo ? $priceMin + random_int(100_000, 500_000) : $priceMin + random_int(5, 20) * 1_000_000;

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

    private function createImages(Property $property, int $index): void
    {
        $extIdx = $index % count($this->exteriorImages);
        $salaIdx = ($index * 3) % count($this->livingRoomImages);
        $kitchenIdx = ($index * 2) % count($this->kitchenImages);
        $bedroomIdx = ($index + 1) % count($this->bedroomImages);
        $bathroomIdx = $index % count($this->bathroomImages);
        $diningIdx = ($index + 1) % count($this->diningImages);

        $gallery = [
            ['path' => self::PX.$this->exteriorImages[$extIdx], 'title' => 'Fachada', 'cover' => true],
            ['path' => self::PX.$this->livingRoomImages[$salaIdx], 'title' => 'Sala', 'cover' => false],
            ['path' => self::PX.$this->kitchenImages[$kitchenIdx], 'title' => 'Cocina', 'cover' => false],
            ['path' => self::PX.$this->bedroomImages[$bedroomIdx], 'title' => 'Habitación principal', 'cover' => false],
            ['path' => self::PX.$this->bathroomImages[$bathroomIdx], 'title' => 'Baño', 'cover' => false],
            ['path' => self::PX.$this->diningImages[$diningIdx], 'title' => 'Comedor', 'cover' => false],
        ];

        foreach ($gallery as $i => $img) {
            Image::create([
                'imageable_id' => $property->id,
                'imageable_type' => Property::class,
                'title' => $img['title'].' — '.$property->code,
                'description' => $img['title'].' de la propiedad '.$property->title,
                'file_name' => basename($img['path']),
                'file_path' => $img['path'],
                'file_extension' => 'jpg',
                'mime_type' => 'image/jpeg',
                'file_size' => 0,
                'width' => 1280,
                'height' => 853,
                'sort_order' => $i,
                'is_cover' => $img['cover'],
                'is_public' => true,
            ]);
        }
    }
}
