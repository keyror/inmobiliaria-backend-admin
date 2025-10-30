<?php

namespace App\Repositories;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;

interface IFiscalProfileRepository
{
    public function create(StoreFiscalProfileRequest $request): void;
    public function update(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): void;
    public function delete(FiscalProfile $fiscalProfile): void;
}
