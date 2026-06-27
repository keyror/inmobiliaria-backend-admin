<?php

namespace Database\Seeders;

use App\Models\AccountBank;
use App\Models\Address;
use App\Models\Contact;
use App\Models\EconomicActivity;
use App\Models\FiscalProfile;
use App\Models\Person;
use App\Models\TaxeType;
use App\Models\User;
use App\Repositories\Implements\LookupRepository;
use App\Support\CalculateDV;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PersonsTableSeeder extends Seeder
{
    public function run(): void
    {
        $lookupRepo = new LookupRepository;
        $lookups = $lookupRepo->getLookupsByCategory([
            'taxe_type',
            'organization_type',
            'document_type',
            'gender',
            'op_si_no',
            'economic_activity',
            'banks',
            'account_banks',
            'stratum',
            'country',
            'department',
            'city',
            'road_type',
            'letter',
            'orientation',
        ]);

        $taxeType = $lookups->get('taxe_type')?->first();
        $orgTypeId = $lookups->get('organization_type')?->first()?->id;
        $docTypeId = $lookups->get('document_type')?->first()?->id;
        $genderId = $lookups->get('gender')?->first()?->id;
        $vatTypeId = $lookups->get('op_si_no')?->first()?->id;
        $economicActivity = $lookups->get('economic_activity')?->first();
        $bankId = $lookups->get('banks')?->first()?->id;
        $accountTypeId = $lookups->get('account_banks')?->first()?->id;
        $stratumId = $lookups->get('stratum')?->first()?->id;
        $countryId = $lookups->get('country')?->first()?->id;
        $departmentId = $lookups->get('department')?->first()?->id;
        $cityId = $lookups->get('city')?->first()?->id;
        $viaTypeId = $lookups->get('road_type')?->first()?->id;
        $letra1Id = $lookups->get('letter')?->first()?->id;
        $orientation1Id = $lookups->get('orientation')?->first()?->id;
        $letra2Id = $lookups->get('letter')?->skip(1)->first()?->id;
        $orientation2Id = $lookups->get('orientation')?->skip(1)->first()?->id;

        foreach (User::all() as $user) {
            $fiscalProfile = FiscalProfile::create([
                'id' => Str::uuid(),
                'responsible_for_vat_type_id' => $vatTypeId,
                'vat_withholding' => 0.00,
                'income_tax_withholding' => 0.00,
                'ica_withholding' => 0.00,
            ]);

            TaxeType::create([
                'taxe_type_id' => $taxeType->id,
                'code' => $taxeType->code,
                'is_principal' => true,
                'fiscal_profile_id' => $fiscalProfile->id,
            ]);

            EconomicActivity::create([
                'economic_activity_type_id' => $economicActivity->id,
                'fiscal_profile_id' => $fiscalProfile->id,
                'is_principal' => true,
                'code' => $economicActivity->code,
            ]);

            $parts = explode('@', $user->email);
            $firstName = ucfirst($parts[0]);
            $lastName = 'Apellido';
            $document = rand(1000, 9999);

            $person = Person::create([
                'user_id' => $user->id,
                'fiscal_profile_id' => $fiscalProfile->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $firstName.' '.$lastName,
                'company_name' => null,
                'document_number' => $document,
                'dv' => CalculateDV::fromNumber($document),
                'document_from_id' => $cityId,
                'organization_type_id' => $orgTypeId,
                'document_type_id' => $docTypeId,
                'gender_type_id' => $genderId,
                'birth_date' => now()->subYears(25),
            ]);

            Contact::create([
                'phone' => '12345678',
                'mobile' => '123456789',
                'email' => $user->email,
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
                'city_id' => $cityId,
                'department_id' => $departmentId,
                'country_id' => $countryId,
                'stratum_id' => $stratumId,
                'zip_code' => '8500001',
                'sector' => 'Llano Vargas',
                'complement' => 'Torre 2',
                'is_principal' => true,
            ]);

            AccountBank::create([
                'person_id' => $person->id,
                'bank_id' => $bankId,
                'account_number' => '123456789',
                'account_type_id' => $accountTypeId,
            ]);
        }
    }
}
