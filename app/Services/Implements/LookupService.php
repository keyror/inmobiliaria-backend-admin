<?php

namespace App\Services\Implements;

use App\Repositories\ILookupRepository;
use App\Services\ILookupService;
use Illuminate\Http\JsonResponse;

class LookupService implements ILookupService
{
    public function __construct(
        private readonly ILookupRepository $lookupRepository
    ) {}

    public function getLookupsByCategory(array $categories): JsonResponse
    {
        $lookups = $this->lookupRepository->getLookupsByCategory($categories);

        return response()->json([
            'status' => true,
            'data' => $lookups,
        ]);
    }

    public function getColombiaWithDepartmentsAndCities(): JsonResponse
    {
        $lookups = $this->lookupRepository->getColombiaWithDepartmentsAndCities();

        return response()->json([
            'status' => true,
            'data' => $lookups,
        ]);
    }
}
