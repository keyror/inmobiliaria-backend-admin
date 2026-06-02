<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IPublicCompanyService
{
    public function show(): JsonResponse;
}
