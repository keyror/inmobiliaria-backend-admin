<?php

namespace App\Services;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

interface IPropertyService
{
    public function getProperties(): JsonResponse;

    public function getProperty(Property $property): JsonResponse;

    public function createProperty(StorePropertyRequest $request): JsonResponse;

    public function updateProperty(UpdatePropertyRequest $request, Property $property): JsonResponse;

    public function deleteProperty(Property $property): JsonResponse;
}
