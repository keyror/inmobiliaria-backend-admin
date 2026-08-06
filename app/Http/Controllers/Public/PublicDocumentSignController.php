<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitSignatureRequest;
use App\Services\IDocumentSignatoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicDocumentSignController extends Controller
{
    public function __construct(
        private readonly IDocumentSignatoryService $signatoryService,
    ) {}

    public function show(string $token, Request $request): JsonResponse
    {
        return $this->signatoryService->getSigningPage($token, $request);
    }

    public function document(string $token): StreamedResponse|JsonResponse
    {
        return $this->signatoryService->getDocumentForSigning($token);
    }

    public function submit(string $token, SubmitSignatureRequest $request): JsonResponse
    {
        return $this->signatoryService->submitSignature($token, $request);
    }

    public function confirmRead(string $token, Request $request): JsonResponse
    {
        return $this->signatoryService->confirmRead($token, $request);
    }
}
