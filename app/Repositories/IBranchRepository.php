<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

interface IBranchRepository
{
    public function findAll(): Collection;

    public function findById(string $id): ?Company;

    public function findHeadquarters(): ?Company;

    /** @return Collection<int, Company> */
    public function findAccessibleForUser(mixed $user): Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): Company;

    /** @param array<string, mixed> $data */
    public function update(Company $branch, array $data): Company;

    public function deactivate(Company $branch): Company;
}
