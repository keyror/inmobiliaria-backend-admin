<?php

namespace App\Services\Implements;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Repositories\IPlanRepository;
use App\Services\IPlanService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PlanService implements IPlanService
{
    public function __construct(
        private readonly IPlanRepository $planRepository
    ) {}

    public function getPlans(): JsonResponse
    {
        try {
            $plans = $this->planRepository->getPlansByFilters();

            return response()->json([
                'status' => true,
                'data' => $plans,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getActivePlans(): JsonResponse
    {
        try {
            $plans = $this->planRepository->getActivePlans();

            return response()->json([
                'status' => true,
                'data' => $plans,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createPlan(StorePlanRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $plan = $this->planRepository->create($request);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('plan.created')],
                'data' => $plan,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updatePlan(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        DB::beginTransaction();
        try {
            $updated = $this->planRepository->update($request, $plan);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('plan.updated')],
                'data' => $updated,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function deletePlan(Plan $plan): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->planRepository->delete($plan);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [__('plan.deleted')],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
