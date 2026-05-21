<?php

namespace App\Services\Implements;

use App\Http\Requests\Public\PublicPropertyIndexRequest;
use App\Http\Resources\Public\PublicPropertyResource;
use App\Http\Resources\Public\PublicPropertyShowResource;
use App\Models\Property;
use App\Repositories\IPublicPropertyRepository;
use App\Services\IPublicPropertyService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PublicPropertyService implements IPublicPropertyService
{
    public function __construct(
        private readonly IPublicPropertyRepository $publicPropertyRepository
    ) {}

    public function getProperties(PublicPropertyIndexRequest $request): JsonResponse
    {
        try {
            $properties = $this->publicPropertyRepository->getPropertiesByFilters();

            return PublicPropertyResource::collection($properties)
                ->additional(['status' => true])
                ->response();
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Property $property): JsonResponse
    {
        try {
            $propertyData = $this->publicPropertyRepository->getPropertyWithRelations($property);

            return (new PublicPropertyShowResource($propertyData))
                ->additional(['status' => true])
                ->response();
        } catch (ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Propiedad no encontrada',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
