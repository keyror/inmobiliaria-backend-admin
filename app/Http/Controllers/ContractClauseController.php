<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractClauseRequest;
use App\Http\Requests\UpdateContractClauseRequest;
use App\Models\ContractClause;
use App\Services\IContractClauseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractClauseController extends Controller
{
    public function __construct(
        private readonly IContractClauseService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->getByTemplate(
            $request->query('template_key', 'arrendamiento_vivienda')
        );
    }

    public function store(StoreContractClauseRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(UpdateContractClauseRequest $request, ContractClause $contractClause): JsonResponse
    {
        return $this->service->update($request, $contractClause);
    }

    public function destroy(ContractClause $contractClause): JsonResponse
    {
        return $this->service->destroy($contractClause);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'template_key' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        return $this->service->reorder($request->template_key, $request->ids);
    }

    public function resetToDefaults(string $templateKey): JsonResponse
    {
        return $this->service->resetToDefaults($templateKey);
    }

    public function preview(string $templateKey): JsonResponse
    {
        return $this->service->preview($templateKey);
    }

    public function meta(): JsonResponse
    {
        return $this->service->meta();
    }
}
