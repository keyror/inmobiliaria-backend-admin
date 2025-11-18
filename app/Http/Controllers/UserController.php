<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\IUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly IUserService $userService
    ){}

    /**
     * Listar usuarios
     */
    public function index(): JsonResponse
    {
        return $this->userService->getUsers();
    }

    /**
     * Crear usuario
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->userService->createUser($request);
    }

    /**
     * Actualizar usuario
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return $this->userService->updateUser($user, $request);
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user): JsonResponse
    {
        return $this->userService->delete($user);
    }

    /**
     * Exportar Excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        return $this->userService->exportExcel();
    }

    /**
     * Exportar PDF
     */
    public function exportPdf(): Response
    {
        return $this->userService->exportPdf();
    }

    public function show(User $user):JsonResponse
    {
        return $this->userService->getUser($user);
    }
}
