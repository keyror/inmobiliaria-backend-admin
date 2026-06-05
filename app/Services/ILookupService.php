<?php

namespace App\Services;

use App\Http\Requests\ManageLookupIndexRequest;
use App\Http\Requests\StoreLookupRequest;
use App\Http\Requests\UpdateLookupRequest;
use App\Models\Lookup;
use Illuminate\Http\JsonResponse;

interface ILookupService
{
    public function getLookups(ManageLookupIndexRequest $request): JsonResponse;

    public function getLookupsByCategory(array $categories): JsonResponse;

    public function getColombiaWithDepartmentsAndCities(): JsonResponse;

    public function getCategories(): JsonResponse;

    public function getLookup(Lookup $lookup): JsonResponse;

    public function createLookup(StoreLookupRequest $request): JsonResponse;

    public function updateLookup(UpdateLookupRequest $request, Lookup $lookup): JsonResponse;

    public function deleteLookup(Lookup $lookup): JsonResponse;
}
