<?php

namespace App\Repositories\Implements;

use App\Models\EconomicActivity;
use App\Repositories\IEconomicActivityRepository;

class EconomicActivityRepository implements IEconomicActivityRepository
{

    public function create(array $data): EconomicActivity
    {
        return EconomicActivity::create([
            'code' => $data['code'] ?? null,
            'description' => $data['description'] ?? null,
            'economic_activity_type_id' => $data['economic_activity_type_id'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
            'fiscal_profile_id' => $data['fiscal_profile_id'] ?? null,
        ]);
    }

    public function update(EconomicActivity $economicActivity, array $data): void
    {
        // TODO: Implement update() method.
    }

    public function delete(EconomicActivity $economicActivity): void
    {
        // TODO: Implement delete() method.
    }
}
