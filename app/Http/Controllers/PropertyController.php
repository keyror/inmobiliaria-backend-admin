<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\IPropertyService;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function __construct(
        private readonly IPropertyService $propertyService
    ) {}

    public function index(): JsonResponse
    {
        return $this->propertyService->getProperties();
    }

    public function show(Property $property) {
        return $this->propertyService->getProperty($property);
    }
}
