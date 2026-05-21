<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Services\ICompanyService;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function __construct(
        private readonly ICompanyService $companyService
    ) {}

    public function show(): JsonResponse
    {
        return $this->companyService->getCompany();
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        return $this->companyService->createCompany($request);
    }

    public function update(UpdateCompanyRequest $request): JsonResponse
    {
        return $this->companyService->updateCompany($request);
    }
}
