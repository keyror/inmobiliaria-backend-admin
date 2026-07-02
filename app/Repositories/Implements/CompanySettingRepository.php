<?php

namespace App\Repositories\Implements;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Repositories\ICompanySettingRepository;

class CompanySettingRepository implements ICompanySettingRepository
{
    public function findByCompany(Company $company): ?CompanySetting
    {
        return $company->setting;
    }

    public function upsert(Company $company, array $data): CompanySetting
    {
        $setting = $company->setting ?? new CompanySetting(['company_id' => $company->id]);

        $setting->fill($data);
        $setting->save();

        return $setting;
    }
}
