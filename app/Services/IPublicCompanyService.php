<?php

namespace App\Services;

use App\Http\Requests\Public\PublicCompanyContactRequest;
use Illuminate\Http\JsonResponse;

interface IPublicCompanyService
{
    public function show(): JsonResponse;

    public function showCentral(): JsonResponse;

    public function showCentralSite(): JsonResponse;

    public function sendCentralContact(PublicCompanyContactRequest $request): JsonResponse;
}
