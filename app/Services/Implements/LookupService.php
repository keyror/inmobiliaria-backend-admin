<?php

namespace App\Services\Implements;

use App\Http\Resources\ColombiaLookupResource;
use App\Http\Resources\LookupResource;
use App\Repositories\ILookupRepository;
use App\Services\ILookupService;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LookupService implements ILookupService
{
    private const int CACHE_TTL_SECONDS = 86400;

    public function __construct(
        private readonly ILookupRepository $lookupRepository
    ) {}

    public function getLookupsByCategory(array $categories): JsonResponse
    {
        $normalizedCategories = $this->normalizeCategories($categories);
        $lookups = Cache::remember(
            CacheKeys::lookupsByCategories($normalizedCategories),
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->lookupRepository
                ->getLookupsByCategory($normalizedCategories)
                ->map(fn ($items): array => LookupResource::collection($items)->resolve(request()))
                ->all()
        );

        return response()->json([
            'status' => true,
            'data' => $lookups,
        ]);
    }

    public function getColombiaWithDepartmentsAndCities(): JsonResponse
    {
        $lookups = Cache::remember(
            CacheKeys::colombiaLookups(),
            self::CACHE_TTL_SECONDS,
            function (): ?array {
                $country = $this->lookupRepository->getColombiaWithDepartmentsAndCities();

                return $country
                    ? (new ColombiaLookupResource($country))->resolve(request())
                    : null;
            }
        );

        return response()->json([
            'status' => true,
            'data' => $lookups,
        ]);
    }

    /**
     * @param  array<int, string>  $categories
     * @return array<int, string>
     */
    private function normalizeCategories(array $categories): array
    {
        return collect($categories)
            ->map(fn (string $category): string => trim($category))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
