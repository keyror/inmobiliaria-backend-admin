<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface IUserService
{
    public function getUsers(): JsonResponse;
    public function exportExcel(): BinaryFileResponse;
    public function exportPdf(): Response;
}
