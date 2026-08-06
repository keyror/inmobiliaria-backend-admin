<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreDocumentSignatoryRequest;
use App\Http\Requests\SubmitSignatureRequest;
use App\Http\Resources\DocumentSignatoryResource;
use App\Mail\DocumentSignatureCompletedMail;
use App\Mail\DocumentSignatureMail;
use App\Mail\DocumentSignatureRejectedMail;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentSignatory;
use App\Models\Lookup;
use App\Models\Rent;
use App\Models\TemplateSection;
use App\Repositories\IDocumentSignatoryRepository;
use App\Services\IDocumentSignatoryService;
use App\Support\FrontendUrl;
use App\Support\TenantMailer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentSignatoryService implements IDocumentSignatoryService
{
    private const CONSENT_TEXT = 'Declaro que he leído el documento completo y que esta firma electrónica tiene validez legal en Colombia según la Ley 527 de 1999. Acepto el documento tal como fue presentado en esta sesión.';

    public function __construct(
        private readonly IDocumentSignatoryRepository $signatoryRepository,
        private readonly DocumentPdfService $pdfService,
        private readonly DocumentTimestampService $timestampService,
    ) {}

    public function getSignatories(Document $document, Rent $rent): JsonResponse
    {
        try {
            $existing = $this->signatoryRepository->getByDocument($document);

            if ($existing->isNotEmpty()) {
                return response()->json([
                    'status' => true,
                    'data' => DocumentSignatoryResource::collection($existing),
                ]);
            }

            $proposed = $this->proposeSignatories($document, $rent);

            return response()->json([
                'status' => true,
                'data' => $proposed,
                'proposed' => true,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function storeSignatories(StoreDocumentSignatoryRequest $request, Document $document): JsonResponse
    {
        if (! $this->documentIsGenerado($document)) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.cannot_modify'),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $this->signatoryRepository->deleteAllPendingForDocument($document);

            foreach ($request->validated()['signatories'] as $index => $data) {
                $this->signatoryRepository->create([
                    'document_id' => $document->id,
                    'person_id' => $data['person_id'] ?? null,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => $data['role'],
                    'order' => $data['order'] ?? ($index + 1),
                ]);
            }

            DB::commit();

            $signatories = $this->signatoryRepository->getByDocument($document);

            return response()->json([
                'status' => true,
                'data' => DocumentSignatoryResource::collection($signatories),
                'message' => __('document_signatory.created'),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function removeSignatory(DocumentSignatory $signatory): JsonResponse
    {
        if ($signatory->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.cannot_delete'),
            ], 422);
        }

        $this->signatoryRepository->delete($signatory);

        return response()->json([
            'status' => true,
            'message' => __('document_signatory.deleted'),
        ]);
    }

    public function sendForSigning(Document $document): JsonResponse
    {
        if (! $this->documentIsGenerado($document)) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.must_be_generated'),
            ], 422);
        }

        $signatories = $this->signatoryRepository->getByDocument($document)
            ->where('status', 'pending');

        if ($signatories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.no_signatories'),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $company = Company::with('setting')->first();
            ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

            foreach ($signatories as $signatory) {
                $signUrl = FrontendUrl::resolve('admin/firmar/'.$signatory->token);
                $mailer->to($signatory->email)->send(
                    new DocumentSignatureMail($document, $signatory, $company, $signUrl, $from)
                );
                $signatory->fill(['sent_at' => now()])->save();
            }

            $enviadoId = Lookup::where('category', 'document_status')
                ->where('alias', 'enviado')
                ->value('id');

            $document->status_id = $enviadoId;
            $document->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('document_signatory.sent'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function resendSignatory(DocumentSignatory $signatory): JsonResponse
    {
        try {
            $document = $signatory->document->load('documentable');
            $company = Company::with('setting')->first();
            ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

            $signUrl = FrontendUrl::resolve('admin/firmar/'.$signatory->token);
            $mailer->to($signatory->email)->send(
                new DocumentSignatureMail($document, $signatory, $company, $signUrl, $from)
            );

            $signatory->fill(['sent_at' => now()])->save();

            return response()->json([
                'status' => true,
                'message' => __('document_signatory.resent'),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getSigningPage(string $token, Request $request): JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory) {
            Log::warning('sign.token_not_found', ['token_prefix' => substr($token, 0, 8), 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if ($signatory->token_expires_at?->isPast()) {
            if ($signatory->status === 'pending' || $signatory->status === 'viewed') {
                $this->signatoryRepository->update($signatory, ['status' => 'expired']);
            }

            Log::warning('sign.token_expired', ['token_prefix' => substr($token, 0, 8), 'signatory_id' => $signatory->id, 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.token_expired')], 410);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
            Log::warning('sign.token_already_used', ['token_prefix' => substr($token, 0, 8), 'signatory_id' => $signatory->id, 'status' => $signatory->status, 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.already_processed')], 422);
        }

        if ($signatory->status === 'pending') {
            $this->signatoryRepository->update($signatory, [
                'status' => 'viewed',
                'viewed_at' => now(),
                'view_ip_address' => $request->ip(),
                'view_user_agent' => $request->userAgent(),
            ]);
            $signatory->status = 'viewed';
            $signatory->viewed_at = now();
        }

        $document = $signatory->document;
        $company = Company::first();

        return response()->json([
            'status' => true,
            'data' => [
                'signatory' => new DocumentSignatoryResource($signatory),
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'number' => $document->number,
                ],
                'company' => [
                    'name' => $company->tradename ?: $company->company_name,
                    'logo_url' => $company->logo?->public_url ?? null,
                ],
            ],
        ]);
    }

    public function getDocumentForSigning(string $token): StreamedResponse|JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory || $signatory->token_expires_at?->isPast()) {
            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
            return response()->json(['status' => false, 'message' => __('document_signatory.already_processed')], 422);
        }

        $document = $signatory->document;

        if (! $document->file_path || ! Storage::disk('local')->exists($document->file_path)) {
            return response()->json(['status' => false, 'message' => __('document.pdf_not_ready')], 404);
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name ?? 'documento.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function submitSignature(string $token, SubmitSignatureRequest $request): JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory) {
            Log::warning('sign.submit_token_not_found', ['token_prefix' => substr($token, 0, 8), 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if ($signatory->token_expires_at?->isPast()) {
            $this->signatoryRepository->update($signatory, ['status' => 'expired']);

            Log::warning('sign.submit_token_expired', ['token_prefix' => substr($token, 0, 8), 'signatory_id' => $signatory->id, 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.token_expired')], 410);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
            Log::warning('sign.submit_token_already_used', ['token_prefix' => substr($token, 0, 8), 'signatory_id' => $signatory->id, 'status' => $signatory->status, 'ip' => $request->ip()]);

            return response()->json(['status' => false, 'message' => __('document_signatory.already_processed')], 422);
        }

        $validated = $request->validated();
        $action = $validated['action'];

        DB::beginTransaction();
        try {
            if ($action === 'reject') {
                $this->signatoryRepository->update($signatory, [
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $this->notifyAdminOfRejection($signatory);
            } else {
                $file = $request->file('signature');
                $path = $file->store('signatures/'.$signatory->document_id, 'local');

                $document = $signatory->document;

                $this->signatoryRepository->update($signatory, [
                    'status' => 'signed',
                    'signature_type' => $validated['signature_type'],
                    'signature_path' => $path,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'consent_accepted_at' => now(),
                    'consent_text' => self::CONSENT_TEXT,
                    'document_hash_at_signing' => $document->pdf_hash,
                ]);

                if ($this->signatoryRepository->allSignedForDocument($document)) {
                    $this->completeSigningProcess($document);
                }
            }

            DB::commit();

            $message = $action === 'reject'
                ? __('document_signatory.rejected')
                : __('document_signatory.signed');

            return response()->json(['status' => true, 'message' => $message]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function confirmRead(string $token, Request $request): JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory || $signatory->token_expires_at?->isPast()) {
            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
            return response()->json(['status' => true]);
        }

        if (! $signatory->document_read_at) {
            $this->signatoryRepository->update($signatory, ['document_read_at' => now()]);
        }

        return response()->json(['status' => true]);
    }

    public function generateCertificate(Document $document): StreamedResponse|JsonResponse
    {
        $firmadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'firmado')
            ->value('id');

        if ($document->status_id !== $firmadoId) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.document_not_signed'),
            ], 422);
        }

        $certificatePdf = $this->pdfService->generateCertificatePdf($document);
        $filename = 'certificado_evidencias_'.($document->number ?? $document->id).'_'.now()->format('Ymd_His').'.pdf';

        return response()->streamDownload(function () use ($certificatePdf) {
            echo $certificatePdf;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function downloadTsr(Document $document): StreamedResponse|JsonResponse
    {
        if (! $document->tsa_token) {
            return response()->json(['status' => false, 'message' => 'Este documento no tiene sello TSA.'], 404);
        }

        $binary = base64_decode($document->tsa_token);
        $filename = 'sello_rfc3161_'.($document->number ?? $document->id).'.tsr';

        return response()->streamDownload(function () use ($binary) {
            echo $binary;
        }, $filename, ['Content-Type' => 'application/timestamp-reply']);
    }

    public function downloadTsq(Document $document): StreamedResponse|JsonResponse
    {
        if (! $document->pdf_hash) {
            return response()->json(['status' => false, 'message' => 'Este documento no tiene hash registrado.'], 404);
        }

        try {
            $binary = $this->timestampService->buildTsqForDocument($document);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }

        $filename = 'peticion_tsa_'.($document->number ?? $document->id).'.tsq';

        return response()->streamDownload(function () use ($binary) {
            echo $binary;
        }, $filename, ['Content-Type' => 'application/timestamp-query']);
    }

    public function resendCompletion(Document $document, bool $includeCertificate): JsonResponse
    {
        $firmadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'firmado')
            ->value('id');

        if ($document->status_id !== $firmadoId) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.document_not_signed'),
            ], 422);
        }

        try {
            $company = Company::with('setting')->first();
            ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

            $certificatePdf = $includeCertificate
                ? $this->pdfService->generateCertificatePdf($document)
                : null;

            $signatories = $this->signatoryRepository->getByDocument($document)
                ->where('status', 'signed');

            foreach ($signatories as $signatory) {
                $mailer->to($signatory->email)->send(
                    new DocumentSignatureCompletedMail($document, $company, $from, $certificatePdf)
                );
            }

            return response()->json([
                'status' => true,
                'message' => __('document_signatory.completion_resent'),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function resendCompletionForSignatory(Document $document, DocumentSignatory $signatory, bool $includeCertificate): JsonResponse
    {
        $firmadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'firmado')
            ->value('id');

        if ($document->status_id !== $firmadoId) {
            return response()->json([
                'status' => false,
                'message' => __('document_signatory.document_not_signed'),
            ], 422);
        }

        if ($signatory->status !== 'signed') {
            return response()->json(['status' => false, 'message' => 'El firmante no ha completado la firma.'], 422);
        }

        try {
            $company = Company::with('setting')->first();
            ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

            $certificatePdf = $includeCertificate
                ? $this->pdfService->generateCertificatePdf($document)
                : null;

            $mailer->to($signatory->email)->send(
                new DocumentSignatureCompletedMail($document, $company, $from, $certificatePdf)
            );

            return response()->json(['status' => true, 'message' => __('document_signatory.completion_resent')]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Privados
    // ──────────────────────────────────────────────────────────────────────────

    private function documentIsGenerado(Document $document): bool
    {
        $generadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'generado')
            ->value('id');

        return $document->status_id === $generadoId;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function proposeSignatories(Document $document, Rent $rent): array
    {
        $signatureSection = TemplateSection::query()
            ->where('template_key', $document->template_key)
            ->where('section_type', 'signature')
            ->where('is_active', true)
            ->first();

        if (! $signatureSection) {
            return [];
        }

        $config = is_array($signatureSection->section_config) ? $signatureSection->section_config : [];
        $sigConfig = $config['signatories'] ?? [];

        $rent->load([
            'rentTenantCodebtors.tenant:id,full_name,company_name,document_number',
            'rentTenantCodebtors.tenant.contacts',
            'rentTenantCodebtors.codebtor:id,full_name,company_name,document_number',
            'rentTenantCodebtors.codebtor.contacts',
            'property.owners:id,full_name,company_name,document_number',
            'property.owners.contacts',
        ]);

        $company = Company::with('contacts')->first();
        $order = 1;
        $proposed = [];

        foreach ($sigConfig as $sig) {
            $role = $sig['role'] ?? 'arrendatario';

            switch ($role) {
                case 'arrendatario':
                    foreach ($rent->rentTenantCodebtors as $pair) {
                        if ($pair->tenant) {
                            $p = $pair->tenant;
                            $proposed[] = $this->buildProposed($p->id, $p->full_name ?? $p->company_name, $this->principalEmail($p), $role, $order++);
                        }
                    }
                    break;

                case 'codeudor':
                    foreach ($rent->rentTenantCodebtors as $pair) {
                        if ($pair->codebtor) {
                            $p = $pair->codebtor;
                            $proposed[] = $this->buildProposed($p->id, $p->full_name ?? $p->company_name, $this->principalEmail($p), $role, $order++);
                        }
                    }
                    break;

                case 'propietario':
                    foreach ($rent->property->owners as $owner) {
                        $proposed[] = $this->buildProposed($owner->id, $owner->full_name ?? $owner->company_name, $this->principalEmail($owner), $role, $order++);
                    }
                    break;

                case 'arrendador':
                default:
                    $proposed[] = $this->buildProposed(
                        null,
                        $company->company_name,
                        $this->principalEmail($company),
                        'arrendador',
                        $order++
                    );
                    break;
            }
        }

        return $proposed;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildProposed(?string $personId, string $name, ?string $email, string $role, int $order): array
    {
        return [
            'id' => null,
            'person_id' => $personId,
            'name' => $name,
            'email' => $email ?? '',
            'role' => $role,
            'order' => $order,
            'status' => 'pending',
        ];
    }

    private function principalEmail(object $contactable): ?string
    {
        $contacts = $contactable->contacts ?? collect();

        $principal = $contacts->firstWhere('is_principal', true);

        return $principal?->email ?? $contacts->whereNotNull('email')->first()?->email;
    }

    private function completeSigningProcess(Document $document): void
    {
        // Preservar PDF original como versión archivada antes de sobrescribir
        $archivadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'archivado')
            ->value('id');

        Document::create([
            'documentable_id' => $document->documentable_id,
            'documentable_type' => $document->documentable_type,
            'company_id' => $document->company_id,
            'document_type_id' => $document->document_type_id,
            'document_category_id' => $document->document_category_id,
            'title' => $document->title.' (original)',
            'file_name' => $document->file_name,
            'file_path' => $document->file_path,
            'file_extension' => $document->file_extension,
            'mime_type' => $document->mime_type,
            'file_size' => $document->file_size,
            'number' => $document->number,
            'template_key' => $document->template_key,
            'content' => $document->content,
            'status_id' => $archivadoId,
            'generated_at' => $document->generated_at,
            'pdf_hash' => $document->pdf_hash,
            'notes' => 'Versión original antes de la firma electrónica',
            'parent_document_id' => $document->id,
            'created_by' => $document->created_by,
        ]);

        // Generar PDF final con firmas incrustadas (actualiza pdf_hash vía saveQuietly interno)
        $result = $this->pdfService->generateSigned($document);

        $firmadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'firmado')
            ->value('id');

        $document->file_path = $result['path'];
        $document->file_name = $result['filename'];
        $document->file_size = $result['size'];
        $document->status_id = $firmadoId;
        $document->signed_at = now();
        $document->save();

        // Sello notarial RFC 3161 — no bloquea si FreeTSA falla
        $this->timestampService->stampDocument($document);

        // Certificado de evidencias
        $certificatePdf = $this->pdfService->generateCertificatePdf($document);

        $company = Company::with('setting')->first();
        ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

        $signatories = $this->signatoryRepository->getByDocument($document)
            ->where('status', 'signed');

        foreach ($signatories as $signatory) {
            $mailer->to($signatory->email)->send(
                new DocumentSignatureCompletedMail($document, $company, $from, $certificatePdf)
            );
        }
    }

    private function notifyAdminOfRejection(DocumentSignatory $signatory): void
    {
        try {
            $signatory->load('document');
            $company = Company::with('setting')->first();
            ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

            $adminEmail = $from?->address ?? config('mail.from.address');
            $mailer->to($adminEmail)->send(
                new DocumentSignatureRejectedMail($signatory, $company, $from)
            );
        } catch (Exception) {
            // No bloquear el flujo del firmante si falla la notificación
        }
    }
}
