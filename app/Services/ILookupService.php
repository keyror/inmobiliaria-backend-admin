<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface ILookupService
{
    public function getLookupsByCategory(array $categories): JsonResponse;
}
