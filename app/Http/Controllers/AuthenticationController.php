<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticationRequest;
use App\Services\IAuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(
        private readonly IAuthenticationService $authenticationService
    ) {}

    public function login(AuthenticationRequest $request): JsonResponse
    {
        return $this->authenticationService->login($request);
    }

    public function logout(): JsonResponse {
        return $this->authenticationService->logout();
    }

    public function refresh(): JsonResponse {
        return $this->authenticationService->refresh();
    }

    public function me(): JsonResponse {
        return $this->authenticationService->me();
    }

    public function sendResetEmail(Request $request): JsonResponse {
        return $this->authenticationService->sendResetEmail($request);
    }

    public function resetPassword(Request $request): JsonResponse {
        return $this->authenticationService->resetPassword($request);
    }
}
