<?php

namespace App\Http\Controllers;

use App\Services\IUserService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly IUserService $userService
    ){}

    public function index()
    {
        return $this->userService->getUsers();
    }

    public function exportExcel(): BinaryFileResponse
    {
        return $this->userService->exportExcel();
    }

    public function exportPdf(): Response
    {
        return $this->userService->exportPdf();
    }
}
