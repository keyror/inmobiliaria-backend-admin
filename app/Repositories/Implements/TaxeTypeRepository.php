<?php

namespace App\Repositories\Implements;

use App\Models\TaxeType;
use App\Repositories\ITaxeTypeRepository;

class TaxeTypeRepository implements ITaxeTypeRepository
{

    public function create(array $data): TaxeType
    {
        return TaxeType::create([
            'code' => $data['code'] ?? null,
            'description' => $data['description'] ?? null,
            'taxe_type_id' => $data['taxe_type_id'] ?? null,
            'is_principal' => $data['is_principal'] ?? false,
            'fiscal_profile_id' => $data['fiscal_profile_id'] ?? null,
        ]);
    }

    public function update(TaxeType $taxeType, array $data): void
    {
        // TODO: Implement update() method.
    }

    public function delete(TaxeType $taxeType): void
    {
        // TODO: Implement delete() method.
    }
}
