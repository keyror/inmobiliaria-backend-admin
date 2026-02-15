<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Http\JsonResponse;

interface IPropertyService
{
    public function getProperties(): JsonResponse;

    public function getProperty(Property $property): JsonResponse;
}
