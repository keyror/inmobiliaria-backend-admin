<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use App\Services\IFiscalProfileService;
use Illuminate\Http\JsonResponse;

class FiscalProfileController extends Controller
{
    public function __construct(
        private readonly IFiscalProfileService $fiscalProfileService
    ) {}

    /**
     * Crear nuevo perfil fiscal
     * POST /fiscal-profiles
     */
    public function store(StoreFiscalProfileRequest $request): JsonResponse
    {
        return $this->fiscalProfileService->createFiscalProfile($request);
    }

    /**
     * Actualizar un perfil fiscal
     * PUT /fiscal-profiles/{fiscalProfile}
     */
    public function update(UpdateFiscalProfileRequest $request, FiscalProfile $fiscalProfile): JsonResponse
    {
        return $this->fiscalProfileService->updateFiscalProfile($fiscalProfile, $request);
    }

    /**
     * Eliminar un perfil fiscal
     * DELETE /fiscal-profiles/{fiscalProfile}
     */
    public function destroy(FiscalProfile $fiscalProfile): JsonResponse
    {
        return $this->fiscalProfileService->deleteFiscalProfile($fiscalProfile);
    }
}
