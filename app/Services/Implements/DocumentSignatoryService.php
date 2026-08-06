<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreDocumentSignatoryRequest;
use App\Http\Requests\SubmitSignatureRequest;
use App\Http\Resources\DocumentSignatoryResource;
use App\Mail\DocumentSignatureCompletedMail;
use App\Mail\DocumentSignatureMail;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentSignatoryService implements IDocumentSignatoryService
{
    public function __construct(
        private readonly IDocumentSignatoryRepository $signatoryRepository,
        private readonly DocumentPdfService $pdfService,
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
            }

            $enviadoId = Lookup::where('category', 'document_status')
                ->where('alias', 'enviado')
                ->value('id');

            $document->status_id = $enviadoId;
            $document->saveQuietly();

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

            return response()->json([
                'status' => true,
                'message' => __('document_signatory.resent'),
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getSigningPage(string $token): JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory) {
            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if ($signatory->token_expires_at?->isPast()) {
            if ($signatory->status === 'pending' || $signatory->status === 'viewed') {
                $this->signatoryRepository->update($signatory, ['status' => 'expired']);
            }

            return response()->json(['status' => false, 'message' => __('document_signatory.token_expired')], 410);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
            return response()->json(['status' => false, 'message' => __('document_signatory.already_processed')], 422);
        }

        if ($signatory->status === 'pending') {
            $this->signatoryRepository->update($signatory, ['status' => 'viewed', 'viewed_at' => now()]);
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

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['status' => false, 'message' => __('document.pdf_not_ready')], 404);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name ?? 'documento.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function submitSignature(string $token, SubmitSignatureRequest $request): JsonResponse
    {
        $signatory = $this->signatoryRepository->findByToken($token);

        if (! $signatory) {
            return response()->json(['status' => false, 'message' => __('document_signatory.token_invalid')], 404);
        }

        if ($signatory->token_expires_at?->isPast()) {
            $this->signatoryRepository->update($signatory, ['status' => 'expired']);

            return response()->json(['status' => false, 'message' => __('document_signatory.token_expired')], 410);
        }

        if (! in_array($signatory->status, ['pending', 'viewed'])) {
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
                $path = $file->store('signatures/'.$signatory->document_id, 'public');

                $this->signatoryRepository->update($signatory, [
                    'status' => 'signed',
                    'signature_type' => $validated['signature_type'],
                    'signature_path' => $path,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $document = $signatory->document;

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
     * Propone firmantes basándose en la sección de firma de la plantilla y las personas del Rent.
     *
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
        $result = $this->pdfService->generateSigned($document);

        $firmadoId = Lookup::where('category', 'document_status')
            ->where('alias', 'firmado')
            ->value('id');

        $document->file_path = $result['path'];
        $document->file_name = $result['filename'];
        $document->file_size = $result['size'];
        $document->status_id = $firmadoId;
        $document->signed_at = now();
        $document->saveQuietly();

        $company = Company::with('setting')->first();
        ['mailer' => $mailer, 'from' => $from] = TenantMailer::resolve($company->setting);

        $signatories = $this->signatoryRepository->getByDocument($document)
            ->where('status', 'signed');

        foreach ($signatories as $signatory) {
            $mailer->to($signatory->email)->send(
                new DocumentSignatureCompletedMail($document, $company, $from)
            );
        }
    }

    private function notifyAdminOfRejection(DocumentSignatory $signatory): void
    {
        // Notificación interna — se puede extender con un Mailable si se requiere en el futuro
        // Por ahora el admin ve el estado directamente en el panel
    }
}
