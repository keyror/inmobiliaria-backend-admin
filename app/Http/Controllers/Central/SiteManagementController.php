<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCentralSiteThemeRequest;
use App\Services\Central\ISiteManagementService;
use Illuminate\Http\JsonResponse;

class SiteManagementController extends Controller
{
    public function __construct(
        private readonly ISiteManagementService $siteManagementService,
    ) {}

    public function showTheme(): JsonResponse
    {
        return $this->siteManagementService->showTheme();
    }

    public function updateTheme(UpdateCentralSiteThemeRequest $request): JsonResponse
    {
        return $this->siteManagementService->updateTheme($request);
    }
}
