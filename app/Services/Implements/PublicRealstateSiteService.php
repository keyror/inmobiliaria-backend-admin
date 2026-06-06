<?php

namespace App\Services\Implements;

use App\Http\Requests\Public\PublicCompanyContactRequest;
use App\Mail\PublicCompanyContactMail;
use App\Models\Company;
use App\Repositories\ICompanyRepository;
use App\Repositories\IRealstateSiteSettingRepository;
use App\Services\IPublicRealstateSiteService;
use App\Support\CacheKeys;
use App\Support\RealstateSiteTemplates;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PublicRealstateSiteService implements IPublicRealstateSiteService
{
    private const int CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private readonly ICompanyRepository $companyRepository,
        private readonly IRealstateSiteSettingRepository $realstateSiteSettingRepository,
    ) {}

    public function show(): JsonResponse
    {
        try {
            $site = Cache::remember(
                CacheKeys::publicRealstateSite(),
                self::CACHE_TTL_SECONDS,
                fn (): ?array => $this->siteData()
            );

            return response()->json([
                'status' => true,
                'data' => $site,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function sendContact(PublicCompanyContactRequest $request): JsonResponse
    {
        try {
            $company = $this->companyRepository->currentPublicWithRelations();

            if (! $company) {
                return response()->json([
                    'status' => false,
                    'message' => [__('company.not_found')],
                ], 404);
            }

            $data = $request->validated();
            $allowedEmails = $company->contacts
                ->pluck('email')
                ->filter()
                ->intersect($data['emails'])
                ->unique()
                ->values()
                ->all();

            if (empty($allowedEmails)) {
                return response()->json([
                    'status' => false,
                    'message' => [__('company.contact_no_recipients')],
                ], 422);
            }

            Mail::to($allowedEmails)->send(
                new PublicCompanyContactMail($company, $data)
            );

            return response()->json([
                'status' => true,
                'message' => [__('company.contact_sent')],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    private function siteData(): ?array
    {
        $company = $this->companyRepository->currentPublicWithRelations();

        if (! $company) {
            return null;
        }

        $setting = $this->realstateSiteSettingRepository->current();
        $configuredTemplateSet = $setting?->template_set ?? RealstateSiteTemplates::DEFAULT_TEMPLATE_SET;
        $templateSet = RealstateSiteTemplates::isValidTemplateSet($configuredTemplateSet)
            ? $configuredTemplateSet
            : RealstateSiteTemplates::DEFAULT_TEMPLATE_SET;
        $pages = $this->normalizedPages($setting?->pages ?? [], $templateSet);

        return [
            'company' => $this->companyData($company),
            'theme' => $this->themeData($setting?->theme),
            'template_set' => $templateSet,
            'templates' => RealstateSiteTemplates::templatesForSet($templateSet),
            'pages' => $this->publicPages($pages),
        ];
    }

    private function companyData(Company $company): array
    {
        $principalContact = $company->contacts->firstWhere('is_principal', true) ?? $company->contacts->first();
        $principalAddress = $company->addresses->firstWhere('is_principal', true) ?? $company->addresses->first();

        return [
            'id' => $company->id,
            'name' => $company->tradename ?: $company->company_name,
            'company_name' => $company->company_name,
            'tradename' => $company->tradename,
            'nit' => $company->nit,
            'logo' => $company->logo?->url,
            'email' => $principalContact?->email,
            'phone' => $principalContact?->mobile ?: $principalContact?->phone,
            'address' => $principalAddress?->address,
            'contact' => $this->contactData($principalContact),
            'contacts' => $company->contacts
                ->map(fn (mixed $contact): ?array => $this->contactData($contact))
                ->values()
                ->all(),
            'address_detail' => $this->addressData($principalAddress),
            'addresses' => $company->addresses
                ->map(fn (mixed $address): ?array => $this->addressData($address))
                ->values()
                ->all(),
        ];
    }

    private function themeData(?array $theme): array
    {
        $colors = RealstateSiteTemplates::normalizeTheme($theme);

        return [
            'primary' => $colors['primary'],
            'secondary' => $colors['secondary'],
            'accent' => $colors['accent'],
            'colors' => [
                'primary' => $colors['primary'],
                'secondary' => $colors['secondary'],
                'accent' => $colors['accent'],
            ],
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

    private function publicPages(array $pages): array
    {
        return collect(RealstateSiteTemplates::EDITABLE_PAGES)
            ->mapWithKeys(fn (string $page): array => [
                $page => ($pages[$page]['is_active'] ?? false)
                    ? ($pages[$page]['content'] ?? [])
                    : [],
            ])
            ->all();
    }

    private function contactData(mixed $contact): ?array
    {
        if (! $contact) {
            return null;
        }

        return [
            'id' => $contact->id,
            'phone' => $contact->phone,
            'mobile' => $contact->mobile,
            'email' => $contact->email,
            'is_principal' => $contact->is_principal,
        ];
    }

    private function addressData(mixed $address): ?array
    {
        if (! $address) {
            return null;
        }

        return [
            'id' => $address->id,
            'address' => $address->address,
            'city' => $this->lookupData($address->city),
            'department' => $this->lookupData($address->department),
            'country' => $this->lookupData($address->country),
            'is_principal' => $address->is_principal,
        ];
    }

    private function lookupData(mixed $lookup): ?array
    {
        if (! $lookup) {
            return null;
        }

        return [
            'id' => $lookup->id,
            'name' => $lookup->name,
            'alias' => $lookup->alias,
        ];
    }
}
