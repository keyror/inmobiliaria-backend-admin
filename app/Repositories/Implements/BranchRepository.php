<?php

namespace App\Repositories\Implements;

use App\Models\Company;
use App\Repositories\IBranchRepository;
use Illuminate\Database\Eloquent\Collection;

class BranchRepository implements IBranchRepository
{
    private function withRelations(mixed $query): mixed
    {
        return $query->with([
            'logo',
            'contacts',
            'addresses.city:id,name,alias',
            'addresses.department:id,name,alias',
            'addresses.country:id,name,alias',
        ]);
    }

    public function findAll(): Collection
    {
        return $this->withRelations(Company::query())
            ->orderByRaw('parent_company_id IS NOT NULL')
            ->orderBy('company_name')
            ->get();
    }

    public function findById(string $id): ?Company
    {
        return $this->withRelations(Company::query())->find($id);
    }

    public function findHeadquarters(): ?Company
    {
        return Company::whereNull('parent_company_id')->first();
    }

    public function findAccessibleForUser(mixed $user): Collection
    {
        if ($user->can('companies.view_all')) {
            return $this->findAll();
        }

        return $this->withRelations($user->companies())->get();
    }

    public function create(array $data): Company
    {
        return Company::create([
            'parent_company_id' => $data['parent_company_id'],
            'branch_code' => $data['branch_code'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'company_name' => $data['company_name'],
            'tradename' => $data['tradename'] ?? null,
            'nit' => $data['nit'],
            'legal_representative_id' => $data['legal_representative_id'] ?? null,
            'person_attendant_id' => $data['person_attendant_id'] ?? null,
        ]);
    }

    public function update(Company $branch, array $data): Company
    {
        $branch->update([
            'branch_code' => $data['branch_code'] ?? $branch->branch_code,
            'is_active' => $data['is_active'] ?? $branch->is_active,
            'company_name' => $data['company_name'] ?? $branch->company_name,
            'tradename' => $data['tradename'] ?? $branch->tradename,
            'nit' => $data['nit'] ?? $branch->nit,
            'legal_representative_id' => $data['legal_representative_id'] ?? $branch->legal_representative_id,
            'person_attendant_id' => $data['person_attendant_id'] ?? $branch->person_attendant_id,
        ]);

        return $branch;
    }

    public function deactivate(Company $branch): Company
    {
        $branch->update(['is_active' => false]);

        return $branch;
    }
}
