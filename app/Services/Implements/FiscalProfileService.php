<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreFiscalProfileRequest;
use App\Http\Requests\UpdateFiscalProfileRequest;
use App\Models\FiscalProfile;
use App\Models\Person;
use App\Repositories\IFiscalProfileRepository;
use App\Services\IFiscalProfileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class FiscalProfileService implements IFiscalProfileService
{
    public function __construct(
        private readonly IFiscalProfileRepository $fiscalProfileRepository
    ) {}

    /**
     * @throws Throwable
     */
    public function createFiscalProfile(StoreFiscalProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->fiscalProfileRepository->create($request);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('fiscal_profile.created')]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function updateFiscalProfile(FiscalProfile $fiscalProfile, UpdateFiscalProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->fiscalProfileRepository->update($fiscalProfile, $request);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => [__('fiscal_profile.updated')]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteFiscalProfile(FiscalProfile $fiscalProfile): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->fiscalProfileRepository->delete($fiscalProfile);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => [__('fiscal_profile.deleted')]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function syncForEconomicActivity(Person $person, array $economicActivies): void
    {
        if (empty($economicActivies)) return;

        $relation = $person->fiscalProfile->economicActivities();

        $relation->delete();

        foreach ($economicActivies as $activityTypeId) {
            $relation->create([
                'economic_activity_type_id' => $activityTypeId,
                'fiscal_profile_id' => $person->fiscal_profile_id
            ]);
        }
    }

    public function syncForTaxeType(Person $person, array $taxesType): void
    {
        if (empty($taxesType)) return;

        $relation = $person->fiscalProfile->taxeTypes();

        $relation->delete();

        foreach ($taxesType as $taxeTypeId) {
            $relation->create([
                'taxe_type_id' => $taxeTypeId,
                'fiscal_profile_id' => $person->fiscal_profile_id
            ]);
        }

    }
}
