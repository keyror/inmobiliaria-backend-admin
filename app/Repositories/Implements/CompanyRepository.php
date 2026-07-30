<?php

namespace App\Repositories\Implements;

use App\Models\Company;
use App\Repositories\ICompanyRepository;

class CompanyRepository implements ICompanyRepository
{
    private function resolveCurrentId(): ?string
    {
        $user = auth()->user();

        if ($user) {
            $id = $user->companies()->wherePivot('is_default', true)->value('companies.id');
            if ($id) {
                return $id;
            }
        }

        return Company::query()->oldest()->value('id');
    }

    public function current(): ?Company
    {
        $id = $this->resolveCurrentId();

        return $id ? Company::find($id) : null;
    }

    public function currentWithRelations(): ?Company
    {
        $id = $this->resolveCurrentId();

        if (! $id) {
            return null;
        }

        return Company::query()
            ->with([
                'legalRepresentative:id,full_name,document_number',
                'personAttendant:id,full_name,document_number',
                'fiscalProfile.vatType:id,name,alias',
                'logo',
                'contacts',
                'accountBanks',
                'addresses.city:id,name,alias',
                'addresses.department:id,name,alias',
                'addresses.country:id,name,alias',
                'publishChannels',
                'setting',
            ])
            ->find($id);
    }

    public function currentPublicWithRelations(): ?Company
    {
        return Company::query()
            ->select(['id', 'company_name', 'tradename', 'nit'])
            ->with([
                'logo:id,imageable_id,imageable_type,file_path,title',
                'contacts:id,contactable_type,contactable_id,phone,mobile,email,is_principal',
                'addresses' => function ($query) {
                    $query
                        ->select(['id', 'addressable_type', 'addressable_id', 'address', 'city_id', 'department_id', 'country_id', 'is_principal'])
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
            'uses_branches' => $data['uses_branches'] ?? $company->uses_branches,
        ]);

        return $company;
    }
}
