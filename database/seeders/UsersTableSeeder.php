<?php

namespace Database\Seeders;

use App\Models\EconomicActivity;
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
            'vat_type'
        ]);

        $usersData = [
            ['email' => 'camilomancipe@outlook.com', 'password' => '123456789'],
            ['email' => 'jhon.doe@example.com', 'password' => '123456789'],
            ['email' => 'maria.perez@example.com', 'password' => '123456789'],
        ];

        foreach ($usersData as $data) {

            // Obtener ids de lookups de forma segura
            $taxeTypeId = $lookups->get('taxe_type')?->first()?->id ?? null;
            $organizationTypeId = $lookups->get('organization_type')?->first()?->id ?? null;
            $documentTypeId = $lookups->get('document_type')?->first()?->id ?? null;
            $genderTypeId = $lookups->get('gender')?->first()?->id ?? null;
            $userStatusTypeId = $lookups->get('user_status')?->first()?->id ?? null;
            $vatTypeId = $lookups->get('vat_type')?->first()?->id ?? null;

            // Crear usuario
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status_type_id' => $userStatusTypeId
            ]);

            // Crear fiscal profile
            $fiscalProfile = FiscalProfile::create([
                'id' => Str::uuid(),
                'taxe_type_id' => $taxeTypeId,
                'responsible_for_vat_type_id' => $vatTypeId,
                'vat_withholding' => 0.00,
                'income_tax_withholding' => 0.00,
                'ica_withholding' => 0.00,
            ]);

            // Crear persona
            $parts = explode('@', $data['email']);
            $firstName = ucfirst($parts[0]);
            $lastName = 'Apellido';
            $fullName = $firstName . ' ' . $lastName;

            $document = rand(1000, 9999);
            $dv = CalculateDV::fromNumber($document);

            Person::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'fiscal_profile_id' => $fiscalProfile->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $fullName,
                'company_name' => null,
                'document_number' => $document,
                'dv' => $dv,
                'document_from' => 'Ciudad',
                'organization_type_id' => $organizationTypeId,
                'document_type_id' => $documentTypeId,
                'gender_type_id' => $genderTypeId,
                'birth_date' => now()->subYears(25),
            ]);
        }
    }
}
