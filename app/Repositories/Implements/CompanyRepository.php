<?php

namespace App\Repositories\Implements;

use App\Models\Company;
use App\Repositories\ICompanyRepository;

class CompanyRepository implements ICompanyRepository
{
    public function current(): ?Company
    {
        return Company::query()->oldest()->first();
    }

    public function currentWithRelations(): ?Company
    {
        return Company::query()
            ->with([
                'legalRepresentative:id,full_name,document_number',
                'personAttendant:id,full_name,document_number',
                'fiscalProfile.vatType:id,name,alias',
                'logo',
                'contacts',
                'addresses.city:id,name,alias',
                'addresses.department:id,name,alias',
                'addresses.country:id,name,alias',
            ])
            ->oldest()
            ->first();
    }

    public function create(array $data): Company
    {
        return Company::create([
            'company_name' => $data['company_name'],
            'tradename' => $data['tradename'] ?? null,
            'nit' => $data['nit'],
            'legal_representative_id' => $data['legal_representative_id'] ?? null,
            'person_attendant_id' => $data['person_attendant_id'] ?? null,
            'fiscal_profile_id' => $data['fiscal_profile_id'] ?? null,
        ]);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update([
            'company_name' => $data['company_name'] ?? $company->company_name,
            'tradename' => $data['tradename'] ?? $company->tradename,
            'nit' => $data['nit'] ?? $company->nit,
            'legal_representative_id' => $data['legal_representative_id'] ?? $company->legal_representative_id,
            'person_attendant_id' => $data['person_attendant_id'] ?? $company->person_attendant_id,
            'fiscal_profile_id' => $data['fiscal_profile_id'] ?? $company->fiscal_profile_id,
        ]);

        return $company;
    }
}
