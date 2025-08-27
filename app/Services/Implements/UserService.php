<?php

namespace App\Services\Implements;

use App\Exports\excel\UsersExport;
use App\Exports\pdf\UsersExportPdf;
use App\Repositories\IUserRepository;
use App\Services\IUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserService implements IUserService
{
    public function __construct(
        private IUserRepository $userRepository
    ){}

    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->getUsersByFilters();
        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    /**
     * @throws Exception
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
}
