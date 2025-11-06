<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface ILookupRepository
{
    public function getLookupsByCategory(array $categories): Collection;
}
