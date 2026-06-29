<?php

namespace App\Services;

use App\Models\Plan;

interface IPlanLimitService
{
    public function getPlan(): ?Plan;

    public function hasActiveSubscription(): bool;

    public function checkUserLimit(): void;

    public function checkPropertyLimit(): void;

    public function checkImageLimit(int $imagesCount): void;
}
