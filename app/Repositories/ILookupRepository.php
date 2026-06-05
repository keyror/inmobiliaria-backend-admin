<?php

namespace App\Repositories;

use App\Models\Lookup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ILookupRepository
{
    public function getLookupsByFilters(): LengthAwarePaginator;

    public function getLookupsByCategory(array $categories): Collection;

    public function getColombiaWithDepartmentsAndCities(): ?Lookup;

    public function getCategories(): Collection;

    public function create(array $data): Lookup;

    public function update(Lookup $lookup, array $data): Lookup;

    public function delete(Lookup $lookup): void;
}
