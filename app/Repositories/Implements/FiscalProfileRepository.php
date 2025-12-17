<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use App\Repositories\IFiscalProfileRepository;

class FiscalProfileRepository implements IFiscalProfileRepository
{
    public function create(StoreFiscalProfileRequest $request): FiscalProfile
    {
         return FiscalProfile::create([
            'tax_regime' => $request->tax_regime,
            'responsible_for_vat_type_id' => $request->responsible_for_vat_type_id,
            'vat_withholding' => $request->vat_withholding ,
            'income_tax_withholding' => $request->income_tax_withholding,
            'ica_withholding' => $request->ica_withholding ,
            'taxe_type_id' => $request->taxe_type_id,
        ]);

    }

    public function update(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): void
    {
        $fiscalProfile->update([
            'tax_regime' => $request->tax_regime,
            'responsible_for_vat' => $request->responsible_for_vat,
            'vat_withholding' => $request->vat_withholding,
            'income_tax_withholding' => $request->income_tax_withholding,
            'ica_withholding' => $request->ica_withholding,
            'economic_activity' => $request->economic_activity,
            'dv' => $request->dv,
            'taxe_type_id' => $request->taxe_type_id,
        ]);
    }

    public function delete(FiscalProfile $fiscalProfile): void
    {
        $fiscalProfile->delete();
    }
}
