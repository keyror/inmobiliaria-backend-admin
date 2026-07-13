<?php

namespace App\Services;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Rent;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface IDocumentService
{
    public function getDocumentsByRent(Rent $rent): JsonResponse;

    public function getDocument(Document $document): JsonResponse;

    public function createDocument(StoreDocumentRequest $request, Rent $rent): JsonResponse;

    public function updateDocument(UpdateDocumentRequest $request, Document $document): JsonResponse;

    public function deleteDocument(Document $document): JsonResponse;

    public function generatePdf(Document $document): JsonResponse;

    public function download(Document $document): StreamedResponse|JsonResponse;
}
