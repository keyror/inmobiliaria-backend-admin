<?php

namespace App\Http\Controllers;

use App\Services\ISearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly ISearchService $searchService
    ) {}

    public function global(Request $request): JsonResponse
    {
        $term = trim($request->string('q'));

        if (strlen($term) < 2) {
            return response()->json([
                'status' => true,
                'data' => ['properties' => [], 'people' => [], 'companies' => []],
            ]);
        }

        return $this->searchService->globalSearch($term);
    }
}
