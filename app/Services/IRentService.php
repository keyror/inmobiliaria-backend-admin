<?php

namespace App\Services;

use App\Http\Requests\StoreRentRequest;
use App\Http\Requests\UpdateRentRequest;
use App\Models\Rent;
use Illuminate\Http\JsonResponse;

interface IRentService
{
    public function getRents(): JsonResponse;

    public function getRent(Rent $rent): JsonResponse;

    public function createRent(StoreRentRequest $request): JsonResponse;

    public function updateRent(UpdateRentRequest $request, Rent $rent): JsonResponse;

    public function deleteRent(Rent $rent): JsonResponse;
}
