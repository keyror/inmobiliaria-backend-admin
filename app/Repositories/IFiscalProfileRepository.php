<?php

namespace App\Repositories;

use App\Models\FiscalProfile;

interface IFiscalProfileRepository
{
    public function create(array $data): FiscalProfile;

    public function update(FiscalProfile $fiscalProfile, array $data): void;

    public function upsert(?FiscalProfile $fiscalProfile, array $data): FiscalProfile;

    public function delete(FiscalProfile $fiscalProfile): void;
}
