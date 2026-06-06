<?php

namespace App\Services\Implements;

use App\Http\Requests\UpdateRealstateSitePageRequest;
use App\Http\Requests\UpdateRealstateSiteTemplateRequest;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Services\IRealstateTemplateManagementService;
use App\Support\CacheKeys;
use App\Support\RealstateSiteTemplates;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class RealstateTemplateManagementService implements IRealstateTemplateManagementService
{
    public function __construct(
        private readonly IRealstateSiteSettingRepository $realstateSiteSettingRepository,
    ) {}

    public function showTemplate(): JsonResponse
    {
        try {
            $setting = $this->realstateSiteSettingRepository->firstOrCreateDefault();

            return response()->json([
                'status' => true,
                'data' => $this->templateData($setting->template_set, $setting->theme),
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function updateTemplate(UpdateRealstateSiteTemplateRequest $request): JsonResponse
    {
        try {
            $templateSet = $request->validated('template_set');
            $validated = $request->validated();
            $setting = $this->realstateSiteSettingRepository->firstOrCreateDefault();

            $data = [
                'template_set' => $templateSet,
                'pages' => $this->syncPagesTemplateSet($setting->pages ?? [], $templateSet),
            ];

            if (array_key_exists('theme', $validated)) {
                $data['theme'] = RealstateSiteTemplates::normalizeTheme($validated['theme']);
            }

            $setting = $this->realstateSiteSettingRepository->save($setting, $data);

            Cache::forget(CacheKeys::publicRealstateSite());

            return response()->json([
                'status' => true,
                'message' => [__('realstate_site.template_updated')],
                'data' => $this->templateData($setting->template_set, $setting->theme),
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function pages(): JsonResponse
    {
        try {
            $setting = $this->realstateSiteSettingRepository->firstOrCreateDefault();

            return response()->json([
                'status' => true,
                'data' => [
                    'template_set' => $setting->template_set,
                    'theme' => RealstateSiteTemplates::normalizeTheme($setting->theme),
                    'pages' => $this->normalizedPages($setting->pages ?? [], $setting->template_set),
                ],
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function updatePage(UpdateRealstateSitePageRequest $request, string $page): JsonResponse
    {
        try {
            if (! RealstateSiteTemplates::isEditablePage($page)) {
                return response()->json([
                    'status' => false,
                    'message' => [__('realstate_site.page_not_editable')],
                ], 422);
            }

            $setting = $this->realstateSiteSettingRepository->firstOrCreateDefault();
            $pages = $this->normalizedPages($setting->pages ?? [], $setting->template_set);
            $validated = $request->validated();

            if (($validated['template'] ?? $setting->template_set) !== $setting->template_set) {
                return response()->json([
                    'status' => false,
                    'message' => [__('realstate_site.page_template_mismatch')],
                ], 422);
            }

            $pages[$page] = [
                'is_active' => $validated['is_active'] ?? $pages[$page]['is_active'],
                'template' => $setting->template_set,
                'content' => $validated['content'] ?? $pages[$page]['content'],
            ];

            $this->realstateSiteSettingRepository->save($setting, [
                'pages' => $this->syncPagesTemplateSet($pages, $setting->template_set),
            ]);

            Cache::forget(CacheKeys::publicRealstateSite());

            return response()->json([
                'status' => true,
                'message' => [__('realstate_site.page_updated')],
                'data' => [
                    'page' => $page,
                    'config' => $pages[$page],
                ],
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function templateData(string $templateSet, ?array $theme = null): array
    {
        return [
            'template_set' => $templateSet,
            'theme' => RealstateSiteTemplates::normalizeTheme($theme),
            'templates' => RealstateSiteTemplates::templatesForSet($templateSet),
        ];
    }

    private function normalizedPages(array $pages, string $templateSet): array
    {
        return collect(RealstateSiteTemplates::defaultPages($templateSet))
            ->mapWithKeys(function (array $defaultPage, string $page) use ($pages, $templateSet): array {
                $configuredPage = is_array($pages[$page] ?? null) ? $pages[$page] : [];

                return [
                    $page => [
                        'is_active' => (bool) ($configuredPage['is_active'] ?? $defaultPage['is_active']),
                        'template' => $templateSet,
                        'content' => is_array($configuredPage['content'] ?? null)
                            ? $configuredPage['content']
                            : $defaultPage['content'],
                    ],
                ];
            })
            ->all();
    }

    private function syncPagesTemplateSet(array $pages, string $templateSet): array
    {
        return collect($this->normalizedPages($pages, $templateSet))
            ->map(fn (array $page): array => array_replace($page, ['template' => $templateSet]))
            ->all();
    }

    private function errorResponse(Exception $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $exception->getMessage(),
        ], 400);
    }
}
