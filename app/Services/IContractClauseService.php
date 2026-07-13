<?php

namespace App\Services;

use App\Http\Requests\StoreContractClauseRequest;
use App\Http\Requests\UpdateContractClauseRequest;
use App\Models\ContractClause;
use Illuminate\Http\JsonResponse;

interface IContractClauseService
{
    public function getByTemplate(string $templateKey): JsonResponse;

    public function store(StoreContractClauseRequest $request): JsonResponse;

    public function update(UpdateContractClauseRequest $request, ContractClause $clause): JsonResponse;

    public function destroy(ContractClause $clause): JsonResponse;

    public function reorder(string $templateKey, array $orderedIds): JsonResponse;

    public function resetToDefaults(string $templateKey): JsonResponse;

    public function preview(string $templateKey): JsonResponse;

    public function meta(): JsonResponse;
}
