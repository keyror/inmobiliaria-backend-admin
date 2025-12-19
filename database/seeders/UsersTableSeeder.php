<?php

namespace Database\Seeders;

use App\Models\AccountBank;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EconomicActivity;
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
            'country'
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
                'address' => 'cll 22 22 33',
                'city_id' => $city,
                'department_id' => $department,
                'country_id' => $country,
                'is_principal' => true,
                'stratum_id' => $stratum,
                'zip_code' => '8500001',
                'complement' => 'torre 2',
                'sector' => 'llano vargas'
            ]);

            AccountBank::create([
                'person_id' => $person->id,
                'bank_id' => $banks,
                'account_number' => 'ABCDH123RTT45HJY',
                'account_type_id' => $accountBanks
            ]);
        }
    }
}
