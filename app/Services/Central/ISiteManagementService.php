<?php

namespace App\Services\Central;

use App\Http\Requests\UpdateCentralSiteThemeRequest;
use Illuminate\Http\JsonResponse;

interface ISiteManagementService
{
    public function showTheme(): JsonResponse;

    public function updateTheme(UpdateCentralSiteThemeRequest $request): JsonResponse;
}
