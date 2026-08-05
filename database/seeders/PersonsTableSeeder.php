<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lookup;
use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonsTableSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = Company::whereNull('parent_company_id')->value('id');

        $cc = $this->alias('document_type', 'CC');
        $pn = $this->alias('organization_type', 'PN');
        $bogota = $this->city('Bogotá');
        $medellin = $this->city('Medellín');
        $cali = $this->city('Cali');
        $countryId = $this->id('country', 'Colombia');
        $cundinamarca = $this->id('department', 'Cundinamarca');
        $antioquia = $this->id('department', 'Antioquia');
        $valleDelCauca = $this->id('department', 'Valle del Cauca');
        $clId = $this->alias('road_type', 'CL');
        $crId = $this->alias('road_type', 'CR');
        $stratum3 = $this->alias('stratum', '3');
        $stratum4 = $this->alias('stratum', '4');
        $stratum5 = $this->alias('stratum', '5');
        $genderM = $this->alias('gender', 'M');
        $genderF = $this->alias('gender', 'F');

        $persons = [
            // Propietarios
            [
                'document_number' => '10456789',
                'first_name' => 'Andrés',
                'last_name' => 'Moreno López',
                'full_name' => 'Andrés Moreno López',
                'document_type_id' => $cc,
                'document_from_id' => $bogota,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderM,
                'birth_date' => '1978-03-14',
                'contact' => ['phone' => '6013100001', 'mobile' => '3101234501', 'email' => 'amoreno@demo.test'],
                'address' => ['address' => 'Calle 72 # 10-35', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => $stratum4],
            ],
            [
                'document_number' => '20345678',
                'first_name' => 'Gloria',
                'last_name' => 'Patiño Ríos',
                'full_name' => 'Gloria Patiño Ríos',
                'document_type_id' => $cc,
                'document_from_id' => $medellin,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderF,
                'birth_date' => '1965-07-22',
                'contact' => ['phone' => '6044100002', 'mobile' => '3201234502', 'email' => 'gpatino@demo.test'],
                'address' => ['address' => 'Carrera 43 # 18-22', 'city_id' => $medellin, 'department_id' => $antioquia, 'via_type_id' => $crId, 'stratum_id' => $stratum5],
            ],
            [
                'document_number' => '30234567',
                'first_name' => 'Ricardo',
                'last_name' => 'Salcedo Vargas',
                'full_name' => 'Ricardo Salcedo Vargas',
                'document_type_id' => $cc,
                'document_from_id' => $cali,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderM,
                'birth_date' => '1972-11-05',
                'contact' => ['phone' => '6023100003', 'mobile' => '3151234503', 'email' => 'rsalcedo@demo.test'],
                'address' => ['address' => 'Avenida 6 Norte # 25-40', 'city_id' => $cali, 'department_id' => $valleDelCauca, 'via_type_id' => $clId, 'stratum_id' => $stratum5],
            ],
            [
                'document_number' => '40123456',
                'first_name' => 'Claudia',
                'last_name' => 'Herrera Ospina',
                'full_name' => 'Claudia Herrera Ospina',
                'document_type_id' => $cc,
                'document_from_id' => $bogota,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderF,
                'birth_date' => '1981-04-30',
                'contact' => ['phone' => '6013100004', 'mobile' => '3181234504', 'email' => 'cherrera@demo.test'],
                'address' => ['address' => 'Calle 127 # 7-85', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => $stratum4],
            ],
            // Arrendatarios
            [
                'document_number' => '50987654',
                'first_name' => 'Juan Pablo',
                'last_name' => 'Torres Muñoz',
                'full_name' => 'Juan Pablo Torres Muñoz',
                'document_type_id' => $cc,
                'document_from_id' => $bogota,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderM,
                'birth_date' => '1990-08-17',
                'contact' => ['phone' => null, 'mobile' => '3001234505', 'email' => 'jptorres@demo.test'],
                'address' => ['address' => 'Carrera 15 # 88-20', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $crId, 'stratum_id' => $stratum3],
            ],
            [
                'document_number' => '60876543',
                'first_name' => 'Marcela',
                'last_name' => 'Gómez Suárez',
                'full_name' => 'Marcela Gómez Suárez',
                'document_type_id' => $cc,
                'document_from_id' => $medellin,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderF,
                'birth_date' => '1993-02-09',
                'contact' => ['phone' => null, 'mobile' => '3101234506', 'email' => 'mgomez@demo.test'],
                'address' => ['address' => 'Calle 33 # 76-15', 'city_id' => $medellin, 'department_id' => $antioquia, 'via_type_id' => $clId, 'stratum_id' => $stratum3],
            ],
            [
                'document_number' => '70765432',
                'first_name' => 'Sebastián',
                'last_name' => 'Ramos Peña',
                'full_name' => 'Sebastián Ramos Peña',
                'document_type_id' => $cc,
                'document_from_id' => $cali,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderM,
                'birth_date' => '1988-06-21',
                'contact' => ['phone' => null, 'mobile' => '3161234507', 'email' => 'sramos@demo.test'],
                'address' => ['address' => 'Carrera 100 # 11-70', 'city_id' => $cali, 'department_id' => $valleDelCauca, 'via_type_id' => $crId, 'stratum_id' => $stratum3],
            ],
            [
                'document_number' => '80654321',
                'first_name' => 'Valentina',
                'last_name' => 'Castro Díaz',
                'full_name' => 'Valentina Castro Díaz',
                'document_type_id' => $cc,
                'document_from_id' => $bogota,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderF,
                'birth_date' => '1995-12-03',
                'contact' => ['phone' => null, 'mobile' => '3221234508', 'email' => 'vcastro@demo.test'],
                'address' => ['address' => 'Calle 53 # 22-40', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => $stratum3],
            ],
            // Codeudores
            [
                'document_number' => '90543210',
                'first_name' => 'Felipe',
                'last_name' => 'Martínez Rueda',
                'full_name' => 'Felipe Martínez Rueda',
                'document_type_id' => $cc,
                'document_from_id' => $bogota,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderM,
                'birth_date' => '1985-09-28',
                'contact' => ['phone' => null, 'mobile' => '3141234509', 'email' => 'fmartinez@demo.test'],
                'address' => ['address' => 'Calle 80 # 50-15', 'city_id' => $bogota, 'department_id' => $cundinamarca, 'via_type_id' => $clId, 'stratum_id' => $stratum3],
            ],
            [
                'document_number' => '91432109',
                'first_name' => 'Paola',
                'last_name' => 'Jiménez Acosta',
                'full_name' => 'Paola Jiménez Acosta',
                'document_type_id' => $cc,
                'document_from_id' => $medellin,
                'organization_type_id' => $pn,
                'gender_type_id' => $genderF,
                'birth_date' => '1987-01-15',
                'contact' => ['phone' => null, 'mobile' => '3241234510', 'email' => 'pjimenez@demo.test'],
                'address' => ['address' => 'Carrera 65 # 44-80', 'city_id' => $medellin, 'department_id' => $antioquia, 'via_type_id' => $crId, 'stratum_id' => $stratum3],
            ],
        ];

        foreach ($persons as $data) {
            $contact = $data['contact'];
            $address = $data['address'];
            unset($data['contact'], $data['address']);

            $data['company_id'] = $companyId;

            $person = Person::updateOrCreate(
                ['document_number' => $data['document_number']],
                $data,
            );

            $person->contacts()->updateOrCreate(
                ['email' => $contact['email']],
                array_filter([
                    'phone' => $contact['phone'] ?? null,
                    'mobile' => $contact['mobile'],
                    'is_principal' => true,
                ]),
            );

            $person->addresses()->updateOrCreate(
                ['is_principal' => true],
                array_merge($address, [
                    'country_id' => $countryId,
                    'is_principal' => true,
                ]),
            );
        }
    }

    private function id(string $category, string $name): string
    {
        return Lookup::query()->where('category', $category)->where('name', $name)->value('id');
    }

    private function alias(string $category, string $alias): string
    {
        return Lookup::query()->where('category', $category)->where('alias', $alias)->value('id');
    }

    private function city(string $name): string
    {
        return Lookup::query()->where('category', 'city')->where('name', $name)->value('id');
    }
}
