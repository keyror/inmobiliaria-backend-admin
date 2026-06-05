<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLookupRequest;
use App\Http\Requests\ManageLookupIndexRequest;
use App\Http\Requests\StoreLookupRequest;
use App\Http\Requests\UpdateLookupRequest;
use App\Models\Lookup;
use App\Services\ILookupService;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function __construct(
        private readonly ILookupService $lookupService
    ) {}

    public function manage(ManageLookupIndexRequest $request): JsonResponse
    {
        return $this->lookupService->getLookups($request);
    }

    public function index(IndexLookupRequest $request): JsonResponse
    {
        return $this->lookupService->getLookupsByCategory($request->categories);
    }

    public function getColombiaWithDepartmentsAndCities(): JsonResponse
    {
        return $this->lookupService->getColombiaWithDepartmentsAndCities();
    }

    public function categories(): JsonResponse
    {
        return $this->lookupService->getCategories();
    }

    public function show(Lookup $lookup): JsonResponse
    {
        return $this->lookupService->getLookup($lookup);
    }

    public function store(StoreLookupRequest $request): JsonResponse
    {
        return $this->lookupService->createLookup($request);
    }

    public function update(UpdateLookupRequest $request, Lookup $lookup): JsonResponse
    {
        return $this->lookupService->updateLookup($request, $lookup);
    }

    public function destroy(Lookup $lookup): JsonResponse
    {
        return $this->lookupService->deleteLookup($lookup);
    }
}
