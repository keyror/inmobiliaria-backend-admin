<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicCompanyContactRequest;
use App\Services\IPublicRealstateSiteService;
use Illuminate\Http\JsonResponse;

class PublicRealstateSiteController extends Controller
{
    public function __construct(
        private readonly IPublicRealstateSiteService $publicRealstateSiteService,
    ) {}

    public function show(): JsonResponse
    {
        return $this->publicRealstateSiteService->show();
    }

    public function sendContact(PublicCompanyContactRequest $request): JsonResponse
    {
        return $this->publicRealstateSiteService->sendContact($request);
    }
}
