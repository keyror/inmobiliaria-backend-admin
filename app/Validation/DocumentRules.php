<?php

namespace App\Validation;

class DocumentRules
{
    public static function store(): array
    {
        return [
            'document.document_type_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'document.document_category_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'document.title' => 'required|string|max:255',
            'document.description' => 'sometimes|nullable|string',
            'document.number' => 'sometimes|nullable|string|max:100',
            'document.template_key' => 'sometimes|nullable|string|max:100',
            'document.content' => 'sometimes|nullable|array',
            'document.file_name' => 'sometimes|nullable|string|max:255',
            'document.file_path' => 'sometimes|nullable|string|max:500',
            'document.file_extension' => 'sometimes|nullable|string|max:10',
            'document.mime_type' => 'sometimes|nullable|string|max:100',
            'document.file_size' => 'sometimes|nullable|integer|min:0',
            'document.document_date' => 'sometimes|nullable|date',
            'document.expiry_date' => 'sometimes|nullable|date',
            'document.status_id' => 'sometimes|nullable|uuid|exists:lookups,id',
            'document.notes' => 'sometimes|nullable|string',
            'document.parent_document_id' => 'sometimes|nullable|uuid|exists:documents,id',
            'document.sort_order' => 'sometimes|integer|min:0',
            'document.is_public' => 'sometimes|boolean',
            'document.is_verified' => 'sometimes|boolean',
        ];
    }

    public static function update(): array
    {
        return self::store();
    }
}
