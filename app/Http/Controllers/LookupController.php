<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLookupRequest;
use App\Services\ILookupService;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{

    public function __construct(
        private readonly ILookupService $lookupService
    )
    {
    }

    public function index(IndexLookupRequest $request): JsonResponse
    {
        return $this->lookupService->getLookupsByCategory($request->categories);
    }

}
