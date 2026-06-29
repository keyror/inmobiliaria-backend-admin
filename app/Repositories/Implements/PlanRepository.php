<?php

namespace App\Repositories\Implements;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Repositories\IPlanRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository implements IPlanRepository
{
    public function getPlansByFilters(): LengthAwarePaginator
    {
        return Plan::query()
            ->allowedFilters(['name', 'is_active'])
            ->allowedSorts(['name', 'price', 'max_users', 'max_properties', 'is_active', 'created_at'])
            ->jsonPaginate();
    }

    public function getActivePlans(): Collection
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'description', 'price', 'max_users', 'max_properties', 'max_images_per_property']);
    }

    public function create(StorePlanRequest $request): Plan
    {
        return Plan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'max_users' => $request->max_users,
            'max_properties' => $request->max_properties,
            'max_images_per_property' => $request->max_images_per_property,
            'is_active' => $request->boolean('is_active', true),
            'data' => $request->input('data'),
        ]);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): Plan
    {
        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'max_users' => $request->max_users,
            'max_properties' => $request->max_properties,
            'max_images_per_property' => $request->max_images_per_property,
            'is_active' => $request->boolean('is_active', $plan->is_active),
            'data' => $request->input('data', $plan->data),
        ]);

        return $plan->fresh();
    }

    public function delete(Plan $plan): void
    {
        $plan->delete();
    }
}
