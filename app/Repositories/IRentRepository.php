<?php

namespace App\Repositories;

use App\Models\Rent;
use Illuminate\Pagination\LengthAwarePaginator;

interface IRentRepository
{
    public function getRentsByFilters(?string $companyId = null): LengthAwarePaginator;

    public function getRentWithRelations(Rent $rent): Rent;

    public function create(array $data): Rent;

    public function update(array $data, Rent $rent): void;

    public function delete(Rent $rent): void;
}
