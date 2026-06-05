<?php

namespace App\Services\Implements;

use App\Http\Requests\Public\PublicCompanyContactRequest;
use App\Http\Resources\Public\PublicCompanyResource;
use App\Mail\PublicCompanyContactMail;
use App\Repositories\ICompanyRepository;
use App\Services\IPublicCompanyService;
use App\Support\CacheKeys;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PublicCompanyService implements IPublicCompanyService
{
    private const int CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private readonly ICompanyRepository $companyRepository
    ) {}

    public function show(): JsonResponse
    {
        try {
            $company = Cache::remember(
                CacheKeys::publicCompany(),
                self::CACHE_TTL_SECONDS,
                function (): ?array {
                    $company = $this->companyRepository->currentPublicWithRelations();

                    return $company
                        ? (new PublicCompanyResource($company))->resolve(request())
                        : null;
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

            $data = $request->all();
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
}
