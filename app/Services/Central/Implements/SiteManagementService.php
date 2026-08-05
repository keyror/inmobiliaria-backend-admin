<?php

namespace App\Services\Central\Implements;

use App\Http\Requests\UpdateCentralSiteThemeRequest;
use App\Models\CentralSiteSetting;
use App\Services\Central\ISiteManagementService;
use App\Support\RealstateSiteTemplates;
use Exception;
use Illuminate\Http\JsonResponse;

class SiteManagementService implements ISiteManagementService
{
    private const array DEFAULT_THEME = [
        'primary' => '#46b5d1',
        'secondary' => '#1f2937',
        'accent' => '#f35d43',
    ];

    public function showTheme(): JsonResponse
    {
        try {
            $setting = $this->getOrCreateSetting();

            return response()->json([
                'status' => true,
                'data' => ['theme' => $this->normalizedTheme($setting->theme)],
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function updateTheme(UpdateCentralSiteThemeRequest $request): JsonResponse
    {
        try {
            $setting = $this->getOrCreateSetting();
            $theme = RealstateSiteTemplates::normalizeTheme($request->validated('theme'));

            $setting->update(['theme' => $theme]);

            return response()->json([
                'status' => true,
                'message' => [__('central_site.theme_updated')],
                'data' => ['theme' => $this->normalizedTheme($theme)],
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function getOrCreateSetting(): CentralSiteSetting
    {
        return CentralSiteSetting::query()->oldest()->firstOrCreate(
            [],
            ['theme' => self::DEFAULT_THEME],
        );
    }

    /** @param  array<string, string>|null  $theme */
    private function normalizedTheme(?array $theme): array
    {
        return RealstateSiteTemplates::normalizeTheme($theme);
    }

    private function errorResponse(Exception $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $exception->getMessage(),
        ], 400);
    }
}
