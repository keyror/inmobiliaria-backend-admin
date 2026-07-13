<?php

namespace App\Repositories\Implements;

use App\Models\Document;
use App\Models\Rent;
use App\Repositories\IDocumentRepository;
use Illuminate\Database\Eloquent\Collection;

class DocumentRepository implements IDocumentRepository
{
    public function getDocumentsByRent(Rent $rent): Collection
    {
        return $rent->documents()
            ->with([
                'documentType:id,name,alias',
                'documentCategory:id,name,alias',
                'status:id,name,alias',
            ])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDocumentWithRelations(Document $document): Document
    {
        return $document->load([
            'documentType:id,name,alias',
            'documentCategory:id,name,alias',
            'status:id,name,alias',
            'createdBy:id,name,email',
            'parent:id,title,file_path',
            'children:id,title,file_path,status_id,created_at',
            'signatories',
        ]);
    }

    public function create(array $data, Rent $rent): Document
    {
        return $rent->documents()->create([
            'document_type_id' => $data['document_type_id'] ?? null,
            'document_category_id' => $data['document_category_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'number' => $data['number'] ?? null,
            'template_key' => $data['template_key'] ?? null,
            'content' => $data['content'] ?? null,
            'file_name' => $data['file_name'] ?? 'pending',
            'file_path' => $data['file_path'] ?? '',
            'file_extension' => $data['file_extension'] ?? '',
            'mime_type' => $data['mime_type'] ?? 'application/octet-stream',
            'file_size' => $data['file_size'] ?? 0,
            'document_date' => $data['document_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'status_id' => $data['status_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'parent_document_id' => $data['parent_document_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_public' => $data['is_public'] ?? false,
            'is_verified' => $data['is_verified'] ?? false,
        ]);
    }

    public function update(array $data, Document $document): void
    {
        $document->update([
            'document_type_id' => $data['document_type_id'] ?? $document->document_type_id,
            'document_category_id' => $data['document_category_id'] ?? $document->document_category_id,
            'title' => $data['title'] ?? $document->title,
            'description' => $data['description'] ?? $document->description,
            'number' => $data['number'] ?? $document->number,
            'template_key' => $data['template_key'] ?? $document->template_key,
            'content' => $data['content'] ?? $document->content,
            'file_name' => $data['file_name'] ?? $document->file_name,
            'file_path' => $data['file_path'] ?? $document->file_path,
            'file_extension' => $data['file_extension'] ?? $document->file_extension,
            'mime_type' => $data['mime_type'] ?? $document->mime_type,
            'file_size' => $data['file_size'] ?? $document->file_size,
            'document_date' => $data['document_date'] ?? $document->document_date,
            'expiry_date' => $data['expiry_date'] ?? $document->expiry_date,
            'status_id' => $data['status_id'] ?? $document->status_id,
            'notes' => $data['notes'] ?? $document->notes,
            'parent_document_id' => $data['parent_document_id'] ?? $document->parent_document_id,
            'sort_order' => $data['sort_order'] ?? $document->sort_order,
            'is_public' => $data['is_public'] ?? $document->is_public,
            'is_verified' => $data['is_verified'] ?? $document->is_verified,
        ]);
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }

    public function updateAfterGeneration(Document $document, string $path, string $filename, int $size, ?string $generatedStatusId): void
    {
        $document->update([
            'file_path' => $path,
            'file_name' => $filename,
            'file_extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $size,
            'generated_at' => now(),
            'status_id' => $generatedStatusId ?? $document->status_id,
        ]);
    }
}
