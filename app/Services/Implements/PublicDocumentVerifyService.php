<?php

namespace App\Services\Implements;

use App\Models\Company;
use App\Models\Document;
use App\Services\IPublicDocumentVerifyService;
use Illuminate\Http\JsonResponse;

class PublicDocumentVerifyService implements IPublicDocumentVerifyService
{
    public function verify(string $number): JsonResponse
    {
        $document = Document::with(['signatories', 'status'])
            ->where('number', $number)
            ->first();

        if (! $document) {
            return response()->json(['message' => __('document.verify_not_found')], 404);
        }

        if (! $document->signed_at) {
            return response()->json(['message' => __('document.verify_not_signed')], 422);
        }

        $company = Company::whereNull('parent_company_id')->first(['id', 'company_name', 'nit']);

        $signatories = $document->signatories
            ->where('status', 'signed')
            ->sortBy('signed_at')
            ->values()
            ->map(fn ($s) => [
                'name' => $s->name,
                'role' => $s->role,
                'signed_at' => $s->signed_at?->toIso8601String(),
                'ip_address' => $s->ip_address,
                'document_hash_at_signing' => $s->document_hash_at_signing,
            ]);

        return response()->json([
            'document' => [
                'number' => $document->number,
                'title' => $document->title,
                'template_key' => $document->template_key,
                'status' => $document->status?->name ?? 'firmado',
                'generated_at' => $document->generated_at?->toIso8601String(),
                'signed_at' => $document->signed_at?->toIso8601String(),
                'pdf_hash' => $document->pdf_hash,
                'has_tsr' => $document->has_tsr,
                'tsa_stamped_at' => $document->tsa_stamped_at?->toIso8601String(),
            ],
            'company' => [
                'name' => $company?->company_name,
                'nit' => $company?->nit,
            ],
            'signatories' => $signatories,
        ]);
    }
}
