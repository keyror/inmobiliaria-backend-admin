<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Services\IPlanService;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function __construct(
        private readonly IPlanService $planService
    ) {}

    public function index(): JsonResponse
    {
        return $this->planService->getPlans();
    }

    public function show(Plan $plan): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $plan->load('frequency:id,name,alias'),
        ]);
    }

    public function select(): JsonResponse
    {
        return $this->planService->getActivePlans();
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        return $this->planService->createPlan($request);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        return $this->planService->updatePlan($request, $plan);
    }

    public function destroy(Plan $plan): JsonResponse
    {
        return $this->planService->deletePlan($plan);
    }
}
