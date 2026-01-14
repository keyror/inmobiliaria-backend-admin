<?php

namespace App\Repositories;

use App\Models\EconomicActivity;

interface IEconomicActivityRepository
{
    public function create(array $data): EconomicActivity;
    public function update(EconomicActivity $economicActivity, array $data): void;
    public function delete(EconomicActivity $economicActivity): void;
}
