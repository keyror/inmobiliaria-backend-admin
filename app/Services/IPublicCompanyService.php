<?php

namespace App\Services;

use App\Http\Requests\Public\PublicCompanyContactRequest;
use Illuminate\Http\JsonResponse;

interface IPublicCompanyService
{
    public function show(): JsonResponse;

    public function sendContact(PublicCompanyContactRequest $request): JsonResponse;
}
