<?php

namespace App\Repositories;

use App\Models\TaxeType;

interface ITaxeTypeRepository
{
    public function create(array $data): TaxeType;
    public function update(TaxeType $taxeType, array $data): void;
    public function delete(TaxeType $taxeType): void;
}
