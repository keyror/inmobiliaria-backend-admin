<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lookup;
use App\Models\Person;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = Company::whereNull('parent_company_id')->value('id');

        // Lookups
        $statusId = Lookup::query()->where('category', 'status')->where('name', 'ACTIVO')->value('id');
        $disponible = Lookup::query()->where('category', 'property_status')->where('alias', 'NUEVO')->value('id');
        $usado = Lookup::query()->where('category', 'property_status')->where('alias', 'USADO')->value('id');
        $arriendo = Lookup::query()->where('category', 'offer_type')->where('alias', 'ARRIENDO')->value('id');
        $venta = Lookup::query()->where('category', 'offer_type')->where('alias', 'VENTA')->value('id');
        $apto = Lookup::query()->where('category', 'property_type')->where('alias', 'APARTAMENTO')->value('id');
        $casa = Lookup::query()->where('category', 'property_type')->where('alias', 'CASA')->value('id');
        $local = Lookup::query()->where('category', 'property_type')->where('alias', 'LOCAL')->value('id');
        $oficina = Lookup::query()->where('category', 'property_type')->where('alias', 'OFICINA')->value('id');

        $stratum3 = Lookup::query()->where('category', 'stratum')->where('alias', '3')->value('id');
        $stratum4 = Lookup::query()->where('category', 'stratum')->where('alias', '4')->value('id');
        $stratum5 = Lookup::query()->where('category', 'stratum')->where('alias', '5')->value('id');
        $stratum6 = Lookup::query()->where('category', 'stratum')->where('alias', '6')->value('id');

        $bogota = Lookup::query()->where('category', 'city')->where('name', 'Bogotá')->value('id');
        $medellin = Lookup::query()->where('category', 'city')->where('name', 'Medellín')->value('id');
        $cali = Lookup::query()->where('category', 'city')->where('name', 'Cali')->value('id');
        $countryId = Lookup::query()->where('category', 'country')->where('name', 'Colombia')->value('id');
        $cundinamarca = Lookup::query()->where('category', 'department')->where('name', 'Cundinamarca')->value('id');
        $antioquia = Lookup::query()->where('category', 'department')->where('name', 'Antioquia')->value('id');
        $valleDelCauca = Lookup::query()->where('category', 'department')->where('name', 'Valle del Cauca')->value('id');
        $clId = Lookup::query()->where('category', 'road_type')->where('alias', 'CL')->value('id');
        $crId = Lookup::query()->where('category', 'road_type')->where('alias', 'CR')->value('id');

        $areaTotalType = Lookup::query()->where('category', 'area_type')->where('alias', 'AREA_TOTAL')->value('id');
        $areaConstruidaType = Lookup::query()->where('category', 'area_type')->where('alias', 'AREA_CONSTRUIDA')->value('id');
        $m2 = Lookup::query()->where('category', 'area_unit')->where('alias', 'METROS_CUADRADOS')->value('id');
        $precioArriendo = Lookup::query()->where('category', 'price_type')->where('alias', 'PRECIO_ARRIENDO')->value('id');
        $precioVenta = Lookup::query()->where('category', 'price_type')->where('alias', 'PRECIO_VENTA')->value('id');

        // Propietarios demo (deben existir del PersonsTableSeeder)
        $owner1 = Person::where('document_number', '10456789')->value('id');
        $owner2 = Person::where('document_number', '20345678')->value('id');
        $owner3 = Person::where('document_number', '30234567')->value('id');
        $owner4 = Person::where('document_number', '40123456')->value('id');

        $properties = [
            [
                'code' => 'PROP-000001',
                'title' => 'Apartamento moderno en Chapinero',
                'property_type_id' => $apto,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $disponible,
                'stratum_id' => $stratum4,
                'rooms' => '3',
                'bathrooms' => '2',
                'year_built' => 2018,
                'description' => 'Apartamento en excelentes condiciones, conjunto cerrado con portería 24h, parqueadero y zonas comunes.',
                'price' => 2_800_000,
                'price_type_id' => $precioArriendo,
                'area' => 78.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner1,
                'address' => ['address' => 'Carrera 13 # 63-45', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $crId, 'stratum_id' => $stratum4, 'sector' => 'Chapinero'],
                'latitude' => 4.6493,
                'longitude' => -74.0624,
            ],
            [
                'code' => 'PROP-000002',
                'title' => 'Casa campestre en El Poblado',
                'property_type_id' => $casa,
                'offer_type_id' => $venta,
                'status_id' => $statusId,
                'status_property_id' => $usado,
                'stratum_id' => $stratum6,
                'rooms' => '5',
                'bathrooms' => '4',
                'year_built' => 2010,
                'description' => 'Hermosa casa en El Poblado con jardín, piscina privada y acabados de lujo.',
                'price' => 950_000_000,
                'price_type_id' => $precioVenta,
                'area' => 280.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner2,
                'address' => ['address' => 'Calle 10 # 34-18', 'city_id' => $medellin, 'department_id' => $antioquia, 'via_type_id' => $clId, 'stratum_id' => $stratum6, 'sector' => 'El Poblado'],
                'latitude' => 6.2088,
                'longitude' => -75.5692,
            ],
            [
                'code' => 'PROP-000003',
                'title' => 'Apartamento en Ciudad Jardín',
                'property_type_id' => $apto,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $disponible,
                'stratum_id' => $stratum5,
                'rooms' => '2',
                'bathrooms' => '2',
                'year_built' => 2020,
                'description' => 'Apartamento nuevo, excelente vista, conjunto residencial con gimnasio y salón comunal.',
                'price' => 2_100_000,
                'price_type_id' => $precioArriendo,
                'area' => 60.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner3,
                'address' => ['address' => 'Carrera 106 # 14-45', 'city_id' => $cali, 'department_id' => $valleDelCauca, 'via_type_id' => $crId, 'stratum_id' => $stratum5, 'sector' => 'Ciudad Jardín'],
                'latitude' => 3.3854,
                'longitude' => -76.5427,
            ],
            [
                'code' => 'PROP-000004',
                'title' => 'Local comercial en Usaquén',
                'property_type_id' => $local,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $disponible,
                'stratum_id' => null,
                'rooms' => null,
                'bathrooms' => '1',
                'year_built' => 2005,
                'description' => 'Local en esquina sobre vía principal, excelente flujo peatonal. Apto para restaurante, comercio o servicios.',
                'price' => 4_500_000,
                'price_type_id' => $precioArriendo,
                'area' => 45.0,
                'area_type_id' => $areaConstruidaType,
                'owner_id' => $owner4,
                'address' => ['address' => 'Calle 119 # 6-20', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => null, 'sector' => 'Usaquén'],
                'latitude' => 4.7088,
                'longitude' => -74.0290,
            ],
            [
                'code' => 'PROP-000005',
                'title' => 'Casa en El Laureles',
                'property_type_id' => $casa,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $usado,
                'stratum_id' => $stratum4,
                'rooms' => '4',
                'bathrooms' => '3',
                'year_built' => 2008,
                'description' => 'Amplia casa de dos plantas, garaje para dos vehículos, patio trasero con zona verde.',
                'price' => 3_200_000,
                'price_type_id' => $precioArriendo,
                'area' => 180.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner2,
                'address' => ['address' => 'Carrera 80 # 34-60', 'city_id' => $medellin, 'department_id' => $antioquia, 'via_type_id' => $crId, 'stratum_id' => $stratum4, 'sector' => 'Laureles'],
                'latitude' => 6.2499,
                'longitude' => -75.5894,
            ],
            [
                'code' => 'PROP-000006',
                'title' => 'Apartamento en Rosales',
                'property_type_id' => $apto,
                'offer_type_id' => $venta,
                'status_id' => $statusId,
                'status_property_id' => $disponible,
                'stratum_id' => $stratum6,
                'rooms' => '3',
                'bathrooms' => '3',
                'year_built' => 2022,
                'description' => 'Apartamento de lujo a estrenar, cocina integral, pisos en porcelanato, balcón con vista a los cerros.',
                'price' => 780_000_000,
                'price_type_id' => $precioVenta,
                'area' => 120.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner1,
                'address' => ['address' => 'Calle 71 # 3-22', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => $stratum6, 'sector' => 'Rosales'],
                'latitude' => 4.6575,
                'longitude' => -74.0520,
            ],
            [
                'code' => 'PROP-000007',
                'title' => 'Oficina en Centro Empresarial',
                'property_type_id' => $oficina,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $disponible,
                'stratum_id' => null,
                'rooms' => null,
                'bathrooms' => '2',
                'year_built' => 2015,
                'description' => 'Oficina con recepción, 4 cubículos, sala de juntas y cocineta. Edificio con parqueadero y seguridad.',
                'price' => 5_800_000,
                'price_type_id' => $precioArriendo,
                'area' => 85.0,
                'area_type_id' => $areaConstruidaType,
                'owner_id' => $owner3,
                'address' => ['address' => 'Avenida El Dorado # 69-35', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => null, 'sector' => 'Salitre'],
                'latitude' => 4.6562,
                'longitude' => -74.1057,
            ],
            [
                'code' => 'PROP-000008',
                'title' => 'Apartamento en San Fernando',
                'property_type_id' => $apto,
                'offer_type_id' => $arriendo,
                'status_id' => $statusId,
                'status_property_id' => $usado,
                'stratum_id' => $stratum3,
                'rooms' => '2',
                'bathrooms' => '1',
                'year_built' => 2012,
                'description' => 'Apartamento bien ubicado, cerca a universidades y centros comerciales, transporte fácil.',
                'price' => 1_400_000,
                'price_type_id' => $precioArriendo,
                'area' => 52.0,
                'area_type_id' => $areaTotalType,
                'owner_id' => $owner4,
                'address' => ['address' => 'Carrera 58 # 10-80', 'city_id' => $cali, 'department_id' => $valleDelCauca, 'via_type_id' => $crId, 'stratum_id' => $stratum3, 'sector' => 'San Fernando'],
                'latitude' => 3.4172,
                'longitude' => -76.5265,
            ],
        ];

        foreach ($properties as $data) {
            $price = $data['price'];
            $priceTypeId = $data['price_type_id'];
            $area = $data['area'];
            $areaTypeId = $data['area_type_id'];
            $ownerId = $data['owner_id'];
            $address = $data['address'];
            $lat = $data['latitude'];
            $lng = $data['longitude'];

            unset($data['price'], $data['price_type_id'], $data['area'], $data['area_type_id'], $data['owner_id'], $data['address'], $data['latitude'], $data['longitude']);

            $data['company_id'] = $companyId;
            $data['latitude'] = $lat;
            $data['longitude'] = $lng;

            $property = Property::updateOrCreate(
                ['code' => $data['code']],
                $data,
            );

            // Precio
            if ($priceTypeId) {
                $property->prices()->updateOrCreate(
                    ['price_type_id' => $priceTypeId],
                    ['price' => $price, 'price_min' => $price, 'price_max' => $price],
                );
            }

            // Área
            if ($areaTypeId && $m2) {
                $property->areas()->updateOrCreate(
                    ['area_type_id' => $areaTypeId],
                    ['area_value' => $area, 'area_unit_id' => $m2],
                );
            }

            // Propietario
            if ($ownerId) {
                $property->ownerships()->updateOrCreate(
                    ['person_id' => $ownerId],
                    ['ownership_percentage' => 100, 'is_principal_owner' => $ownerId, 'ownership_start_date' => now()->subYears(2)->toDateString()],
                );
            }

            // Dirección
            $property->addresses()->updateOrCreate(
                ['is_principal' => true],
                array_merge($address, ['country_id' => $countryId, 'is_principal' => true]),
            );
        }
    }
}
