<?php

namespace App\Services;

use App\Http\Requests\StoreTemplateSectionRequest;
use App\Http\Requests\UpdateTemplateSectionRequest;
use App\Models\TemplateSection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

interface ITemplateSectionService
{
    public function getByTemplate(string $templateKey): JsonResponse;

    public function store(StoreTemplateSectionRequest $request): JsonResponse;

    public function update(UpdateTemplateSectionRequest $request, TemplateSection $templateSection): JsonResponse;

    public function destroy(TemplateSection $templateSection): JsonResponse;

    public function reorder(string $templateKey, array $orderedIds): JsonResponse;

    public function resetToDefaults(string $templateKey): JsonResponse;

    /** Streams a PDF preview via DomPDF — returns application/pdf response */
    public function preview(string $templateKey): Response;

    public function meta(): JsonResponse;
}
