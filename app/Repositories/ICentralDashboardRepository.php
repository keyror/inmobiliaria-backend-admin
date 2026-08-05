<?php

namespace App\Repositories;

interface ICentralDashboardRepository
{
    public function getStats(): array;
}
