<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\IPublicDocumentVerifyService;
use Illuminate\Http\JsonResponse;

class PublicDocumentVerifyController extends Controller
{
    public function __construct(
        private readonly IPublicDocumentVerifyService $verifyService,
    ) {}

    public function show(string $number): JsonResponse
    {
        return $this->verifyService->verify($number);
    }
}
