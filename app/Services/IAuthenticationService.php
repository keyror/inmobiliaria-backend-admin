<?php

namespace App\Services;

use App\Http\Requests\AuthenticationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface IAuthenticationService
{
    public function login(AuthenticationRequest $request): JsonResponse;
    public function logout(): JsonResponse;
    public function sendResetEmail(Request $request): JsonResponse;
    public function resetPassword(Request $request): JsonResponse;
}
