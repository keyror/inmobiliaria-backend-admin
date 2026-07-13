<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateSectionRequest;
use App\Http\Requests\UpdateTemplateSectionRequest;
use App\Models\TemplateSection;
use App\Services\ITemplateSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateSectionController extends Controller
{
    public function __construct(
        private readonly ITemplateSectionService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->getByTemplate(
            $request->query('template_key', 'arrendamiento_vivienda')
        );
    }

    public function store(StoreTemplateSectionRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(UpdateTemplateSectionRequest $request, TemplateSection $templateSection): JsonResponse
    {
        return $this->service->update($request, $templateSection);
    }

    public function destroy(TemplateSection $templateSection): JsonResponse
    {
        return $this->service->destroy($templateSection);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'template_key' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        return $this->service->reorder($request->template_key, $request->ids);
    }

    public function resetToDefaults(string $templateKey): JsonResponse
    {
        return $this->service->resetToDefaults($templateKey);
    }

    public function preview(string $templateKey): Response
    {
        return $this->service->preview($templateKey);
    }

    public function meta(): JsonResponse
    {
        return $this->service->meta();
    }
}
