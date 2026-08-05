<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IPublicCompanyService
{
    public function show(): JsonResponse;

    public function showCentral(): JsonResponse;

    public function showCentralSite(): JsonResponse;
}
