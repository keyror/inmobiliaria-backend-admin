<?php

namespace App\Services;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

interface IPlanService
{
    public function getPlans(): JsonResponse;

    public function getActivePlans(): JsonResponse;

    public function createPlan(StorePlanRequest $request): JsonResponse;

    public function updatePlan(UpdatePlanRequest $request, Plan $plan): JsonResponse;

    public function deletePlan(Plan $plan): JsonResponse;
}
