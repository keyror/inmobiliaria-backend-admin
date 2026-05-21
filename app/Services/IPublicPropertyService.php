<?php

namespace App\Services;

use App\Http\Requests\Public\PublicPropertyContactRequest;
use App\Http\Requests\Public\PublicPropertyIndexRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

interface IPublicPropertyService
{
    public function getProperties(PublicPropertyIndexRequest $request): JsonResponse;

    public function show(Property $property): JsonResponse;

    public function sendContact(PublicPropertyContactRequest $request, Property $property): JsonResponse;
}
