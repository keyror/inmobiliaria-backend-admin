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
                'publishChannels',
                'setting',
            ])
            ->oldest()
            ->first();
    }

    public function currentPublicWithRelations(): ?Company
    {
        return Company::query()
            ->select(['id', 'company_name', 'tradename', 'nit'])
            ->with([
                'logo:id,imageable_id,imageable_type,file_path,title',
                'contacts:id,company_id,phone,mobile,email,is_principal',
                'addresses' => function ($query) {
                    $query
                        ->select(['id', 'company_id', 'address', 'city_id', 'department_id', 'country_id', 'is_principal'])
                        ->orderByDesc('is_principal')
                        ->with([
                            'city:id,name,alias',
                            'department:id,name,alias',
                            'country:id,name,alias',
                        ]);
                },
                'publishChannels' => function ($query) {
                    $query->whereNotNull('external_link')->with('channel:id,name,alias');
                },
                'setting:id,company_id,has_custom_smtp,smtp_host,smtp_port,smtp_encryption,smtp_username,smtp_password,smtp_from_email',
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
