<?php

namespace App\Services\Implements;

use App\Models\Plan;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use App\Services\IPlanLimitService;
use Exception;

class PlanLimitService implements IPlanLimitService
{
    public function getPlan(): ?Plan
    {
        /** @var Tenant|null $tenant */
        $tenant = tenancy()->tenant;

        return $tenant?->plan;
    }

    public function hasActiveSubscription(): bool
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            return true;
        }

        $endsAt = $tenant->subscription_ends_at;

        return $endsAt === null || $endsAt->isFuture();
    }

    public function checkUserLimit(): void
    {
        $plan = $this->getPlan();

        if (! $plan) {
            return;
        }

        $count = User::count();

        if ($count >= $plan->max_users) {
            throw new Exception(__('plan.user_limit_reached', ['max' => $plan->max_users]));
        }
    }

    public function checkPropertyLimit(): void
    {
        $plan = $this->getPlan();

        if (! $plan) {
            return;
        }

        $count = Property::count();

        if ($count >= $plan->max_properties) {
            throw new Exception(__('plan.property_limit_reached', ['max' => $plan->max_properties]));
        }
    }

    public function checkImageLimit(int $imagesCount): void
    {
        $plan = $this->getPlan();

        if (! $plan) {
            return;
        }

        if ($imagesCount > $plan->max_images_per_property) {
            throw new Exception(__('plan.image_limit_reached', ['max' => $plan->max_images_per_property]));
        }
    }
}
