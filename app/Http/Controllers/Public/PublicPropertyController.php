<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicPropertyContactRequest;
use App\Http\Requests\Public\PublicPropertyIndexRequest;
use App\Models\Property;
use App\Services\IPublicPropertyService;
use Illuminate\Http\JsonResponse;

class PublicPropertyController extends Controller
{
    public function __construct(
        private readonly IPublicPropertyService $publicPropertyService
    ) {}

    public function index(PublicPropertyIndexRequest $request): JsonResponse
    {
        return $this->publicPropertyService->getProperties($request);
    }

    public function show(Property $property): JsonResponse
    {
        return $this->publicPropertyService->show($property);
    }

    public function sendContact(PublicPropertyContactRequest $request, Property $property): JsonResponse
    {
        return $this->publicPropertyService->sendContact($request, $property);
    }
}
