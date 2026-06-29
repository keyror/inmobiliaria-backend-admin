<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface ISearchService
{
    public function globalSearch(string $term): JsonResponse;
}
