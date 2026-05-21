<?php

namespace App\Services\Implements;

use App\Http\Requests\Public\PublicPropertyContactRequest;
use App\Http\Requests\Public\PublicPropertyIndexRequest;
use App\Http\Resources\Public\PublicPropertyResource;
use App\Http\Resources\Public\PublicPropertyShowResource;
use App\Mail\PublicPropertyContactMail;
use App\Models\Property;
use App\Repositories\IPublicPropertyRepository;
use App\Services\IPublicPropertyService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

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

    public function sendContact(PublicPropertyContactRequest $request, Property $property): JsonResponse
    {
        try {
            $data = $request->all();
            $propertyData = $this->publicPropertyRepository->getPropertyWithRelations($property);
            $allowedEmails = $propertyData->contacts
                ->pluck('email')
                ->filter()
                ->intersect($data['emails'])
                ->unique()
                ->values()
                ->all();

            if (empty($allowedEmails)) {
                return response()->json([
                    'status' => false,
                    'message' => [__('public_property.contact_no_recipients')],
                ], 422);
            }

            Mail::to($allowedEmails)->send(
                new PublicPropertyContactMail($propertyData, $data)
            );

            return response()->json([
                'status' => true,
                'message' => [__('public_property.contact_sent')],
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => [__('public_property.not_found')],
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
