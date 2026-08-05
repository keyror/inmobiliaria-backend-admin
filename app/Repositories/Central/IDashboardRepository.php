<?php

namespace App\Repositories\Central;

interface IDashboardRepository
{
    public function getStats(): array;
}
