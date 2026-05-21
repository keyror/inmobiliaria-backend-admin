<?php

namespace App\Services;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\JsonResponse;

interface ICompanyService
{
    public function getCompany(): JsonResponse;

    public function createCompany(StoreCompanyRequest $request): JsonResponse;

    public function updateCompany(UpdateCompanyRequest $request): JsonResponse;
}
