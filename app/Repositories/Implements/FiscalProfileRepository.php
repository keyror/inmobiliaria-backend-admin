<?php

namespace App\Repositories\Implements;

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
            'rental_fee' => $data['rental_fee'] ?? null,
        ]);
    }

    public function update(FiscalProfile $fiscalProfile, array $data): void
    {
        $fiscalProfile->update([
            'tax_regime' => $data['tax_regime'] ?? null,
            'responsible_for_vat_type_id' => $data['responsible_for_vat_type_id'] ?? null,
            'vat_withholding' => $data['vat_withholding'] ?? null,
            'income_tax_withholding' => $data['income_tax_withholding'] ?? null,
            'ica_withholding' => $data['ica_withholding'] ?? null,
            'rental_fee' => $data['rental_fee'] ?? null,
        ]);
    }

    public function upsert(?FiscalProfile $fiscalProfile, array $data): FiscalProfile
    {
        if ($fiscalProfile) {
            $this->update($fiscalProfile, $data);

            return $fiscalProfile;
        }

        return $this->create($data);
    }

    public function delete(FiscalProfile $fiscalProfile): void
    {
        $fiscalProfile->delete();
    }
}
