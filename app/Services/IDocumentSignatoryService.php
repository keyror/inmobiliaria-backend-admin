<?php

namespace App\Services;

use App\Http\Requests\StoreDocumentSignatoryRequest;
use App\Http\Requests\SubmitSignatureRequest;
use App\Models\Document;
use App\Models\DocumentSignatory;
use App\Models\Rent;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface IDocumentSignatoryService
{
    public function getSignatories(Document $document, Rent $rent): JsonResponse;

    public function storeSignatories(StoreDocumentSignatoryRequest $request, Document $document): JsonResponse;

    public function removeSignatory(DocumentSignatory $signatory): JsonResponse;

    public function sendForSigning(Document $document): JsonResponse;

    public function resendSignatory(DocumentSignatory $signatory): JsonResponse;

    public function getSigningPage(string $token): JsonResponse;

    public function getDocumentForSigning(string $token): StreamedResponse|JsonResponse;

    public function submitSignature(string $token, SubmitSignatureRequest $request): JsonResponse;
}
