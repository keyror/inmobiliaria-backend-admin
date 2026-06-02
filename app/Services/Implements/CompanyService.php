<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repositories\ICompanyRepository;
use App\Services\ICompanyService;
use App\Services\IImageService;
use App\Support\CacheKeys;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyService implements ICompanyService
{
    public function __construct(
        private readonly ICompanyRepository $companyRepository,
        private readonly IImageService $imageService,
    ) {}

    public function getCompany(): JsonResponse
    {
        try {
            $company = $this->companyRepository->currentWithRelations();

            return response()->json([
                'status' => true,
                'data' => $company ? new CompanyResource($company) : null,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function createCompany(StoreCompanyRequest $request): JsonResponse
    {
        return $this->saveCompany($request->all());
    }

    /**
     * @throws Throwable
     */
    public function updateCompany(UpdateCompanyRequest $request): JsonResponse
    {
        return $this->saveCompany($request->all());
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Throwable
     */
    private function saveCompany(array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $company = $this->companyRepository->current();
            $wasCreated = ! $company;
            $companyData = $data['company'] ?? [];

            $company = $company
                ? $this->companyRepository->update($company, $companyData)
                : $this->companyRepository->create($companyData);

            $this->syncRelations($company, $data);

            DB::commit();
            Cache::forget(CacheKeys::publicCompany());

            return response()->json([
                'status' => true,
                'message' => [$wasCreated ? __('company.created') : __('company.updated')],
            ], $wasCreated ? 201 : 200);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncRelations(Company $company, array $data): void
    {
        if (array_key_exists('contacts', $data)) {
            $company->syncHasMany('contacts', $data['contacts'] ?? []);
        }

        if (array_key_exists('addresses', $data)) {
            $company->syncHasMany('addresses', $data['addresses'] ?? []);
        }

        $companyData = $data['company'] ?? [];

        if (array_key_exists('logo_image_id', $companyData)) {
            $images = $companyData['logo_image_id']
                ? [[
                    'id' => $companyData['logo_image_id'],
                    'is_cover' => true,
                    'sort_order' => 0,
                ]]
                : [];

            $this->imageService->syncImages($company, $images);
        }
    }
}
