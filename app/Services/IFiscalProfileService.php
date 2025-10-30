<?php

namespace App\Services;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use Illuminate\Http\JsonResponse;

interface IFiscalProfileService
{
    public function createFiscalProfile(StoreFiscalProfileRequest $request): JsonResponse;
    public function updateFiscalProfile(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): JsonResponse;
    public function deleteFiscalProfile(FiscalProfile $fiscalProfile): JsonResponse;
}
