<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentSignatoryRequest;
use App\Models\Document;
use App\Models\DocumentSignatory;
use App\Models\Rent;
use App\Services\IDocumentSignatoryService;
use Illuminate\Http\JsonResponse;

class DocumentSignatoryController extends Controller
{
    public function __construct(
        private readonly IDocumentSignatoryService $signatoryService,
    ) {}

    public function index(Rent $rent, Document $document): JsonResponse
    {
        return $this->signatoryService->getSignatories($document, $rent);
    }

    public function store(StoreDocumentSignatoryRequest $request, Rent $rent, Document $document): JsonResponse
    {
        return $this->signatoryService->storeSignatories($request, $document);
    }

    public function destroy(Rent $rent, Document $document, DocumentSignatory $signatory): JsonResponse
    {
        return $this->signatoryService->removeSignatory($signatory);
    }

    public function send(Rent $rent, Document $document): JsonResponse
    {
        return $this->signatoryService->sendForSigning($document);
    }

    public function resend(Rent $rent, Document $document, DocumentSignatory $signatory): JsonResponse
    {
        return $this->signatoryService->resendSignatory($signatory);
    }
}
