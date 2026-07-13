<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\Rent;
use Illuminate\Database\Eloquent\Collection;

interface IDocumentRepository
{
    public function getDocumentsByRent(Rent $rent): Collection;

    public function getDocumentWithRelations(Document $document): Document;

    public function create(array $data, Rent $rent): Document;

    public function update(array $data, Document $document): void;

    public function delete(Document $document): void;

    public function updateAfterGeneration(Document $document, string $path, string $filename, int $size, ?string $generatedStatusId): void;
}
