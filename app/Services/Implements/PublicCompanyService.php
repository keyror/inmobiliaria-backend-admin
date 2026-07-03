<?php

namespace App\Services\Implements;

use App\Http\Resources\Public\PublicCompanyResource;
use App\Repositories\ICompanyRepository;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Services\IPublicCompanyService;
use App\Support\CacheKeys;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublicCompanyService implements IPublicCompanyService
{
    private const int CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private readonly ICompanyRepository $companyRepository,
        private readonly IRealstateSiteSettingRepository $siteSettingRepository,
    ) {}

    public function show(): JsonResponse
    {
        try {
            $company = Cache::remember(
                CacheKeys::publicCompany(),
                self::CACHE_TTL_SECONDS,
                function (): ?array {
                    $company = $this->companyRepository->currentPublicWithRelations();

                    if (! $company) {
                        return null;
                    }

                    $data = (new PublicCompanyResource($company))->resolve(request());

                    $setting = $this->siteSettingRepository->current();
                    $data['favicon_url'] = $setting?->pages['layout']['content']['favicon_url'] ?? null;

                    return $data;
                }
            );

            return response()->json([
                'status' => true,
                'data' => $company,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
