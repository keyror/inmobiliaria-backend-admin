<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Rent;
use App\Services\IDocumentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        private readonly IDocumentService $documentService
    ) {}

    public function index(Rent $rent): JsonResponse
    {
        return $this->documentService->getDocumentsByRent($rent);
    }

    public function show(Rent $rent, Document $document): JsonResponse
    {
        return $this->documentService->getDocument($document);
    }

    public function store(StoreDocumentRequest $request, Rent $rent): JsonResponse
    {
        return $this->documentService->createDocument($request, $rent);
    }

    public function update(UpdateDocumentRequest $request, Rent $rent, Document $document): JsonResponse
    {
        return $this->documentService->updateDocument($request, $document);
    }

    public function destroy(Rent $rent, Document $document): JsonResponse
    {
        return $this->documentService->deleteDocument($document);
    }

    public function generate(Rent $rent, Document $document): JsonResponse
    {
        return $this->documentService->generatePdf($document);
    }

    public function download(Rent $rent, Document $document): StreamedResponse|JsonResponse
    {
        return $this->documentService->download($document);
    }
}
