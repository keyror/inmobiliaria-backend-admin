<?php

namespace App\Services;

use App\Http\Requests\UpdateRealstateSitePageRequest;
use App\Http\Requests\UpdateRealstateSiteTemplateRequest;
use Illuminate\Http\JsonResponse;

interface IRealstateTemplateManagementService
{
    public function showTemplate(): JsonResponse;

    public function updateTemplate(UpdateRealstateSiteTemplateRequest $request): JsonResponse;

    public function pages(): JsonResponse;

    public function updatePage(UpdateRealstateSitePageRequest $request, string $page): JsonResponse;

    public function restoreTemplate(): JsonResponse;

    public function restorePage(string $page): JsonResponse;

    public function restoreAll(): JsonResponse;
}
