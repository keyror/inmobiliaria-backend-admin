<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\IPublicCompanyService;
use Illuminate\Http\JsonResponse;

class PublicCompanyController extends Controller
{
    public function __construct(
        private readonly IPublicCompanyService $publicCompanyService
    ) {}

    public function show(): JsonResponse
    {
        return $this->publicCompanyService->show();
    }
}
