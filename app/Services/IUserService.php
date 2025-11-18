<?php

namespace App\Services;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface IUserService
{
    public function getUsers(): JsonResponse;
    public function getUser(User $user): JsonResponse;
    public function exportExcel(): BinaryFileResponse;
    public function exportPdf(): Response;
    public function createUser(StoreUserRequest $request): JsonResponse;
    public function updateUser(User $user, UpdateUserRequest $request): JsonResponse;
    public function delete(User $user): JsonResponse;
}
