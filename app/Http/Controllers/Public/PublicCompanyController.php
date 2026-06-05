<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicCompanyContactRequest;
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

    public function sendContact(PublicCompanyContactRequest $request): JsonResponse
    {
        return $this->publicCompanyService->sendContact($request);
    }
}
