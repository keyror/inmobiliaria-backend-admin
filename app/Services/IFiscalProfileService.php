<?php

namespace App\Services;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

interface IFiscalProfileService
{
    public function createFiscalProfile(StoreFiscalProfileRequest $request): JsonResponse;
    public function updateFiscalProfile(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): JsonResponse;
    public function deleteFiscalProfile(FiscalProfile $fiscalProfile): JsonResponse;
    public function syncForEconomicActivity(Person $person, array $economicActivies): void;
    public function syncForTaxeType(Person $person, array $taxesType): void;
}
