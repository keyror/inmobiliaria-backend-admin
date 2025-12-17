<?php

namespace App\Repositories;

use App\Models\Lookup;
use Illuminate\Support\Collection;

interface ILookupRepository
{
    public function getLookupsByCategory(array $categories): Collection;

    public function getColombiaWithDepartmentsAndCities(): ?Lookup;
}
