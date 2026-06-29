<?php

namespace App\Repositories;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPlanRepository
{
    public function getPlansByFilters(): LengthAwarePaginator;

    public function getActivePlans(): Collection;

    public function create(StorePlanRequest $request): Plan;

    public function update(UpdatePlanRequest $request, Plan $plan): Plan;

    public function delete(Plan $plan): void;
}
