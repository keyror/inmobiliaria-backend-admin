<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface IPublicDocumentVerifyService
{
    public function verify(string $number): JsonResponse;
}
