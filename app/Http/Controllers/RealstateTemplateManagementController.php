<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRealstateSitePageRequest;
use App\Http\Requests\UpdateRealstateSiteTemplateRequest;
use App\Services\IRealstateTemplateManagementService;
use Illuminate\Http\JsonResponse;

class RealstateTemplateManagementController extends Controller
{
    public function __construct(
        private readonly IRealstateTemplateManagementService $realstateTemplateManagementService,
    ) {}

    public function showTemplate(): JsonResponse
    {
        return $this->realstateTemplateManagementService->showTemplate();
    }

    public function updateTemplate(UpdateRealstateSiteTemplateRequest $request): JsonResponse
    {
        return $this->realstateTemplateManagementService->updateTemplate($request);
    }

    public function pages(): JsonResponse
    {
        return $this->realstateTemplateManagementService->pages();
    }

    public function updatePage(UpdateRealstateSitePageRequest $request, string $page): JsonResponse
    {
        return $this->realstateTemplateManagementService->updatePage($request, $page);
    }
}
