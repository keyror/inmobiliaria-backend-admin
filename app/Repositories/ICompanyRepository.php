<?php

namespace App\Repositories;

use App\Models\Company;

interface ICompanyRepository
{
    public function current(): ?Company;

    public function currentWithRelations(): ?Company;

    public function create(array $data): Company;

    public function update(Company $company, array $data): Company;
}
