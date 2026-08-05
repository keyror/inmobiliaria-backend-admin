<?php

namespace App\Services\Implements;

use App\Http\Resources\PlanResource;
use App\Http\Resources\Public\PublicCompanyResource;
use App\Models\CentralSiteSetting;
use App\Repositories\ICompanyRepository;
use App\Repositories\IPlanRepository;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Services\IPublicCompanyService;
use App\Support\CacheKeys;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublicCompanyService implements IPublicCompanyService
{
    private const int CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private readonly ICompanyRepository $companyRepository,
        private readonly IRealstateSiteSettingRepository $siteSettingRepository,
        private readonly IPlanRepository $planRepository,
    ) {}

    public function show(): JsonResponse
    {
        try {
            $company = Cache::remember(
                CacheKeys::publicCompany(),
                self::CACHE_TTL_SECONDS,
                function (): ?array {
                    $company = $this->companyRepository->currentPublicWithRelations();

                    if (! $company) {
                        return null;
                    }

                    $data = (new PublicCompanyResource($company))->resolve(request());

                    $setting = $this->siteSettingRepository->current();
                    $data['favicon_url'] = $setting?->pages['layout']['content']['favicon_url'] ?? null;

                    return $data;
                }
            );

            return response()->json([
                'status' => true,
                'data' => $company,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function showCentral(): JsonResponse
    {
        try {
            $company = Cache::remember(
                CacheKeys::publicCompany(),
                self::CACHE_TTL_SECONDS,
                function (): ?array {
                    $company = $this->companyRepository->currentPublicWithRelations();

                    if (! $company) {
                        return null;
                    }

                    $data = (new PublicCompanyResource($company))->resolve(request());
                    $data['favicon_url'] = null;

                    return $data;
                }
            );

            return response()->json([
                'status' => true,
                'data' => $company,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * Equivalente central de public/realstate/site.
     * Lee empresa desde Company (logo, contactos, redes) igual que el tenant,
     * y tema desde central_site_settings (singleton). Mismo contrato de
     * respuesta que el endpoint tenant para reutilizar la lógica del frontend.
     */
    public function showCentralSite(): JsonResponse
    {
        try {
            $company = $this->companyRepository->currentPublicWithRelations();

            if (! $company) {
                return response()->json(['status' => true, 'data' => null]);
            }

            $setting = CentralSiteSetting::query()->oldest()->first();
            $rawTheme = $setting?->theme ?? [];
            $defaultTheme = ['primary' => '#46b5d1', 'secondary' => '#1f2937', 'accent' => '#f35d43'];
            $theme = array_merge($defaultTheme, array_filter($rawTheme));

            $principalContact = $company->contacts->firstWhere('is_principal', true)
                ?? $company->contacts->first();
            $principalAddress = $company->addresses->firstWhere('is_principal', true)
                ?? $company->addresses->first();

            return response()->json([
                'status' => true,
                'data' => [
                    'company' => [
                        'id' => $company->id,
                        'name' => $company->tradename ?: $company->company_name,
                        'company_name' => $company->company_name,
                        'tradename' => $company->tradename,
                        'nit' => $company->nit,
                        'logo' => $company->logo?->url,
                        'email' => $principalContact?->email,
                        'phone' => $principalContact?->phone,
                        'mobile' => $principalContact?->mobile,
                        'address' => $principalAddress?->address,
                        'contact' => $principalContact ? [
                            'id' => $principalContact->id,
                            'phone' => $principalContact->phone,
                            'mobile' => $principalContact->mobile,
                            'email' => $principalContact->email,
                            'is_principal' => $principalContact->is_principal,
                        ] : null,
                        'contacts' => $company->contacts->map(fn ($c) => [
                            'id' => $c->id,
                            'phone' => $c->phone,
                            'mobile' => $c->mobile,
                            'email' => $c->email,
                            'is_principal' => $c->is_principal,
                        ])->values()->all(),
                        'address_detail' => $principalAddress ? [
                            'id' => $principalAddress->id,
                            'address' => $principalAddress->address,
                            'is_principal' => $principalAddress->is_principal,
                        ] : null,
                        'addresses' => $company->addresses->map(fn ($a) => [
                            'id' => $a->id,
                            'address' => $a->address,
                            'is_principal' => $a->is_principal,
                        ])->values()->all(),
                        'social_links' => $company->publishChannels
                            ->filter(fn ($pc) => $pc->external_link && $pc->channel)
                            ->map(fn ($pc) => [
                                'name' => $pc->channel->name,
                                'alias' => $pc->channel->alias,
                                'url' => $pc->external_link,
                            ])
                            ->values()
                            ->all(),
                    ],
                    'theme' => array_merge($theme, ['colors' => $theme]),
                    'plans' => PlanResource::collection($this->planRepository->getPublicPlans())->resolve(request()),
                    // Reservados para gestión futura del sitio central (CMS)
                    'template_set' => null,
                    'templates' => null,
                    'pages' => null,
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
