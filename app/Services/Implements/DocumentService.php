<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Lookup;
use App\Models\Rent;
use App\Repositories\IDocumentRepository;
use App\Services\IDocumentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\LogBatch;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService implements IDocumentService
{
    public function __construct(
        private readonly IDocumentRepository $documentRepository,
        private readonly DocumentPdfService $pdfService
    ) {}

    public function getDocumentsByRent(Rent $rent): JsonResponse
    {
        try {
            $documents = $this->documentRepository->getDocumentsByRent($rent);

            return response()->json([
                'status' => true,
                'data' => $documents,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getDocument(Document $document): JsonResponse
    {
        try {
            $data = $this->documentRepository->getDocumentWithRelations($document);

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createDocument(StoreDocumentRequest $request, Rent $rent): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $this->documentRepository->create($requestData['document'], $rent);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Documento creado exitosamente'],
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        } finally {
            LogBatch::endBatch();
        }
    }

    public function updateDocument(UpdateDocumentRequest $request, Document $document): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();
        try {
            $requestData = $request->all();

            $this->documentRepository->update($requestData['document'] ?? [], $document);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Documento actualizado exitosamente'],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        } finally {
            LogBatch::endBatch();
        }
    }

    public function deleteDocument(Document $document): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->documentRepository->delete($document);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Documento eliminado exitosamente'],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function generatePdf(Document $document): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();
        try {
            $document->load('documentable');

            $result = $this->pdfService->generate($document);

            $generatedStatusId = Lookup::where('category', 'document_status')
                ->where('alias', 'generado')
                ->value('id');

            $this->documentRepository->updateAfterGeneration(
                $document,
                $result['path'],
                $result['filename'],
                $result['size'],
                $generatedStatusId
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['PDF generado exitosamente'],
                'data' => [
                    'file_name' => $result['filename'],
                    'file_path' => $result['path'],
                ],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        } finally {
            LogBatch::endBatch();
        }
    }

    public function download(Document $document): StreamedResponse|JsonResponse
    {
        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'status' => false,
                'message' => 'El archivo PDF aún no ha sido generado.',
            ], 404);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name ?? 'documento.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
