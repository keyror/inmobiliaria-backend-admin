<?php

namespace App\Repositories\Implements;

use App\Models\Document;
use App\Models\DocumentSignatory;
use App\Repositories\IDocumentSignatoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DocumentSignatoryRepository implements IDocumentSignatoryRepository
{
    public function getByDocument(Document $document): Collection
    {
        return DocumentSignatory::query()
            ->where('document_id', $document->id)
            ->orderBy('order')
            ->get();
    }

    public function create(array $data): DocumentSignatory
    {
        $data['token'] = $data['token'] ?? Str::uuid()->toString();
        $data['token_expires_at'] = $data['token_expires_at'] ?? now()->addDays(30);
        $data['status'] = $data['status'] ?? 'pending';

        return DocumentSignatory::create($data);
    }

    public function deleteAllPendingForDocument(Document $document): void
    {
        DocumentSignatory::query()
            ->where('document_id', $document->id)
            ->where('status', 'pending')
            ->delete();
    }

    public function findByToken(string $token): ?DocumentSignatory
    {
        return DocumentSignatory::query()
            ->with('document')
            ->where('token', $token)
            ->first();
    }

    public function update(DocumentSignatory $signatory, array $data): void
    {
        $signatory->fill($data)->save();
    }

    public function delete(DocumentSignatory $signatory): void
    {
        $signatory->delete();
    }

    public function allSignedForDocument(Document $document): bool
    {
        // Excluye rechazados y expirados: solo activos deben firmar
        $active = DocumentSignatory::where('document_id', $document->id)
            ->whereIn('status', ['pending', 'viewed', 'signed'])
            ->count();

        $signed = DocumentSignatory::where('document_id', $document->id)
            ->where('status', 'signed')
            ->count();

        return $active > 0 && $active === $signed;
    }
}
