<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Repositories\IPropertyRepository;
use App\Services\IImageService;
use App\Services\IPropertyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PropertyService implements IPropertyService
{
    public function __construct(
        private readonly IPropertyRepository $propertyRepository,
        private readonly IImageService $imageService
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
            $propertyData = $this->propertyRepository->getPropertyWithRelations($property);
            return response()->json([
                'status' => true,
                'data' => $propertyData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function createProperty(StorePropertyRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $property = $this->propertyRepository->create($requestData['property']);

            if (!empty($requestData['areas'])) {
                $property->syncHasMany('areas', $requestData['areas']);
            }

            if (!empty($requestData['property']['price'])) {
                $property->syncHasOne('price', $requestData['property']['price']);
            }

            if (isset($requestData['property']['features'])) {
                $property->syncHasMany('features', $requestData['property']['features']);
            }

            if (!empty($requestData['obligations'])) {
                $property->syncHasMany('obligations', $requestData['obligations']);
            }

            if (!empty($requestData['publish_channels'])) {
                $property->syncHasMany('publishChannels', $requestData['publish_channels']);
            }

            if (!empty($requestData['ownerships'])) {
                $property->syncHasMany('ownerships', $requestData['ownerships']);
            }

            if (!empty($requestData['addresses'])) {
                $property->syncHasMany('addresses', $requestData['addresses']);
            }

            if (!empty($requestData['contacts'])) {
                $property->syncHasMany('contacts', $requestData['contacts']);
            }

            if (!empty($requestData['property']['images'])) {
                $this->imageService->syncImages(
                    $property,
                    $requestData['property']['images']
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Propiedad creada exitosamente']
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function updateProperty(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $this->propertyRepository->update($requestData['property'], $property);

            if (isset($requestData['areas'])) {
                $property->syncHasMany('areas', $requestData['areas']);
            }

            if (isset($requestData['property']['price'])) {
                $property->syncHasOne('price', $requestData['property']['price']);
            }

            if (isset($requestData['property']['features'])) {
                $property->syncHasMany('features', $requestData['property']['features']);
            }

            if (isset($requestData['obligations'])) {
                $property->syncHasMany('obligations', $requestData['obligations']);
            }

            if (isset($requestData['publish_channels'])) {
                $property->syncHasMany('publishChannels', $requestData['publish_channels']);
            }

            if (isset($requestData['ownerships'])) {
                $property->syncHasMany(
                    'ownerships',
                    $requestData['ownerships'],
                    'property_id',
                    'person_id'
                );
            }

            if (!empty($requestData['property']['images'])) {
                $this->imageService->syncImages(
                    $property,
                    $requestData['property']['images']
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Propiedad actualizada exitosamente']
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteProperty(Property $property): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->propertyRepository->delete($property);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Propiedad eliminada exitosamente']
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
