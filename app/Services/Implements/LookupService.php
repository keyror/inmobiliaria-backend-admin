<?php

namespace App\Services\Implements;

use App\Http\Requests\ManageLookupIndexRequest;
use App\Http\Requests\StoreLookupRequest;
use App\Http\Requests\UpdateLookupRequest;
use App\Http\Resources\ColombiaLookupResource;
use App\Http\Resources\LookupResource;
use App\Models\Lookup;
use App\Repositories\ILookupRepository;
use App\Services\ILookupService;
use App\Support\CacheKeys;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LookupService implements ILookupService
{
    private const int CACHE_TTL_SECONDS = 86400;

    public function __construct(
        private readonly ILookupRepository $lookupRepository
    ) {}

    public function getLookups(ManageLookupIndexRequest $request): JsonResponse
    {
        try {
            $lookups = $this->lookupRepository->getLookupsByFilters();

            return LookupResource::collection($lookups)
                ->additional(['status' => true])
                ->response();
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function getLookupsByCategory(array $categories): JsonResponse
    {
        $normalizedCategories = $this->normalizeCategories($categories);
        $version = $this->lookupCacheVersion();
        $lookups = Cache::remember(
            CacheKeys::lookupsByCategories($normalizedCategories, $version),
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
        $version = $this->lookupCacheVersion();
        $lookups = Cache::remember(
            CacheKeys::colombiaLookups($version),
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

    public function getCategories(): JsonResponse
    {
        $version = $this->lookupCacheVersion();
        $categories = Cache::remember(
            CacheKeys::lookupCategories($version),
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->lookupRepository->getCategories()->all()
        );

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }

    public function getLookup(Lookup $lookup): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => new LookupResource($lookup),
        ]);
    }

    public function createLookup(StoreLookupRequest $request): JsonResponse
    {
        try {
            $lookup = $this->lookupRepository->create($request->validated());
            $this->invalidateLookupCache();

            return (new LookupResource($lookup))
                ->additional([
                    'status' => true,
                    'message' => [__('lookup.created')],
                ])
                ->response()
                ->setStatusCode(201);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function updateLookup(UpdateLookupRequest $request, Lookup $lookup): JsonResponse
    {
        try {
            $lookup = $this->lookupRepository->update($lookup, $request->validated());
            $this->invalidateLookupCache();

            return (new LookupResource($lookup))
                ->additional([
                    'status' => true,
                    'message' => [__('lookup.updated')],
                ])
                ->response();
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function deleteLookup(Lookup $lookup): JsonResponse
    {
        try {
            $this->lookupRepository->delete($lookup);
            $this->invalidateLookupCache();

            return response()->json([
                'status' => true,
                'message' => [__('lookup.deleted')],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
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

    private function lookupCacheVersion(): int
    {
        return (int) Cache::get(CacheKeys::lookupsVersion(), 1);
    }

    private function invalidateLookupCache(): void
    {
        Cache::forever(CacheKeys::lookupsVersion(), $this->lookupCacheVersion() + 1);
    }
}
