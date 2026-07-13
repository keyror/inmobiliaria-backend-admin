<?php

namespace App\Repositories;

use App\Models\ContractClause;
use Illuminate\Database\Eloquent\Collection;

interface IContractClauseRepository
{
    public function getByTemplate(string $templateKey): Collection;

    public function find(int $id): ?ContractClause;

    public function create(array $data): ContractClause;

    public function update(ContractClause $clause, array $data): ContractClause;

    public function delete(ContractClause $clause): void;

    public function reorder(string $templateKey, array $orderedIds): void;

    public function seedDefaults(string $templateKey, array $clauses): void;
}
