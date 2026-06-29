<?php

namespace App\Services\Implements;

use App\Repositories\ISearchRepository;
use App\Services\ISearchService;
use Exception;
use Illuminate\Http\JsonResponse;

class SearchService implements ISearchService
{
    public function __construct(
        private readonly ISearchRepository $searchRepository
    ) {}

    public function globalSearch(string $term): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->searchRepository->search($term),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
