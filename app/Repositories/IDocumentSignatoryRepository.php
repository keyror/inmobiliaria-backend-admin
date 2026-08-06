<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\DocumentSignatory;
use Illuminate\Database\Eloquent\Collection;

interface IDocumentSignatoryRepository
{
    public function getByDocument(Document $document): Collection;

    public function create(array $data): DocumentSignatory;

    public function deleteAllPendingForDocument(Document $document): void;

    public function findByToken(string $token): ?DocumentSignatory;

    public function update(DocumentSignatory $signatory, array $data): void;

    public function delete(DocumentSignatory $signatory): void;

    public function allSignedForDocument(Document $document): bool;
}
