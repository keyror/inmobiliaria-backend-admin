<?php

namespace App\Services;

use App\Http\Requests\StoreDocumentSignatoryRequest;
use App\Http\Requests\SubmitSignatureRequest;
use App\Models\Document;
use App\Models\DocumentSignatory;
use App\Models\Rent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface IDocumentSignatoryService
{
    public function getSignatories(Document $document, Rent $rent): JsonResponse;

    public function storeSignatories(StoreDocumentSignatoryRequest $request, Document $document): JsonResponse;

    public function removeSignatory(DocumentSignatory $signatory): JsonResponse;

    public function sendForSigning(Document $document): JsonResponse;

    public function resendSignatory(DocumentSignatory $signatory): JsonResponse;

    public function getSigningPage(string $token, Request $request): JsonResponse;

    public function getDocumentForSigning(string $token): StreamedResponse|JsonResponse;

    public function submitSignature(string $token, SubmitSignatureRequest $request): JsonResponse;

    public function generateCertificate(Document $document): StreamedResponse|JsonResponse;

    public function downloadTsr(Document $document): StreamedResponse|JsonResponse;

    public function downloadTsq(Document $document): StreamedResponse|JsonResponse;

    public function resendCompletion(Document $document, bool $includeCertificate): JsonResponse;

    public function confirmRead(string $token, Request $request): JsonResponse;

    public function resendCompletionForSignatory(Document $document, DocumentSignatory $signatory, bool $includeCertificate): JsonResponse;
}
