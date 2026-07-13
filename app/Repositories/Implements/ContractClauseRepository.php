<?php

namespace App\Repositories\Implements;

use App\Models\ContractClause;
use App\Repositories\IContractClauseRepository;
use Illuminate\Database\Eloquent\Collection;

class ContractClauseRepository implements IContractClauseRepository
{
    public function getByTemplate(string $templateKey): Collection
    {
        return ContractClause::forTemplate($templateKey)->get();
    }

    public function find(int $id): ?ContractClause
    {
        return ContractClause::find($id);
    }

    public function create(array $data): ContractClause
    {
        return ContractClause::create($data);
    }

    public function update(ContractClause $clause, array $data): ContractClause
    {
        $clause->update($data);

        return $clause->fresh();
    }

    public function delete(ContractClause $clause): void
    {
        $clause->delete();
    }

    public function reorder(string $templateKey, array $orderedIds): void
    {
        $rows = [];
        foreach ($orderedIds as $order => $id) {
            $rows[] = ['id' => $id, 'sort_order' => $order];
        }

        if (! empty($rows)) {
            ContractClause::upsert($rows, ['id'], ['sort_order']);
        }
    }

    public function seedDefaults(string $templateKey, array $clauses): void
    {
        foreach ($clauses as $clause) {
            ContractClause::firstOrCreate(
                ['template_key' => $templateKey, 'section_key' => $clause['section_key']],
                array_merge($clause, ['is_default' => true])
            );
        }
    }
}
