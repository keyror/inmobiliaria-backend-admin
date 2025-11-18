<?php

namespace App\Services\Implements;

use App\Exports\excel\UsersExport;
use App\Exports\pdf\UsersExportPdf;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\IUserRepository;
use App\Services\IUserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class UserService implements IUserService
{
    public function __construct(
        private readonly IUserRepository $userRepository
    ) {}

    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->getUsersByFilters();
        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportExcel(): BinaryFileResponse
    {
        $users = $this->userRepository->getUsersByFilters();
        return Excel::download(new UsersExport($users->items()), 'users.xlsx');
    }

    public function exportPdf(): Response
    {
        $users = $this->userRepository->getUsersByFilters();
        $usersExport = new UsersExportPdf($users->items());
        return $usersExport->export();
    }

    /**
     * Crear usuario
     * @throws Throwable
     */
    public function createUser(StoreUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->userRepository->createUser($request);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => [__('user.created')]
            ], 201);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Actualizar usuario
     * @throws Throwable
     */
    public function updateUser(User $user, UpdateUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->userRepository->updateUser($user, $request);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => [__('user.updated')]
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Eliminar usuario
     * @throws Throwable
     */
    public function delete(User $user): JsonResponse
    {
        DB::beginTransaction();
        try {

            $this->userRepository->delete($user);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => [__('user.deleted')]
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUser(User $user): JsonResponse
    {
        $users = $this->userRepository->getUser($user);
        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }
}
