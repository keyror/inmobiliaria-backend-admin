<?php

namespace App\Services\Implements;

use App\Models\Property;
use App\Repositories\IPropertyRepository;
use App\Services\IPropertyService;
use Exception;
use Illuminate\Http\JsonResponse;

class PropertyService implements IPropertyService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly IPropertyRepository $propertyRepository
    ) {}

    public function getProperties(): JsonResponse
    {
        try {
            $properties = $this->propertyRepository->getPropertiesByFilters();
            return response()->json([
                'status' => true,
                'data' => $properties,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getProperty(Property $property): JsonResponse
    {
        try {
            $properties = $this->propertyRepository->getPropertyWithRelations($property);
            return response()->json([
                'status' => true,
                'data' => $properties,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
