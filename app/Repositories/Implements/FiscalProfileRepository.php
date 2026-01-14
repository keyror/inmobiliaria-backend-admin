<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use App\Repositories\IFiscalProfileRepository;

class FiscalProfileRepository implements IFiscalProfileRepository
{
    public function create(array $data): FiscalProfile
    {
        return FiscalProfile::create([
            'tax_regime' => $data['tax_regime'] ?? null,
            'responsible_for_vat_type_id' => $data['responsible_for_vat_type_id'] ?? null,
            'vat_withholding' => $data['vat_withholding'] ?? null,
            'income_tax_withholding' => $data['income_tax_withholding'] ?? null,
            'ica_withholding' => $data['ica_withholding'] ?? null,
            'rental_fee' => $data['rental_fee'] ?? null
        ]);
    }

    public function update(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): void
    {
        $fiscalProfile->update([
            'tax_regime' => $data['tax_regime'] ?? null,
            'responsible_for_vat_type_id' => $data['responsible_for_vat_type_id'] ?? null,
            'vat_withholding' => $data['vat_withholding'] ?? null,
            'income_tax_withholding' => $data['income_tax_withholding'] ?? null,
            'ica_withholding' => $data['ica_withholding'] ?? null,
            'rental_fee' => $data['rental_fee'] ?? null
        ]);
    }

    public function delete(FiscalProfile $fiscalProfile): void
    {
        $fiscalProfile->delete();
    }
}
