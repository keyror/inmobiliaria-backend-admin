<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CompanySetting;

interface ICompanySettingRepository
{
    public function findByCompany(Company $company): ?CompanySetting;

    public function upsert(Company $company, array $data): CompanySetting;
}
