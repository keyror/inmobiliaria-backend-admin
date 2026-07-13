<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentRequest;
use App\Http\Requests\UpdateRentRequest;
use App\Models\Rent;
use App\Services\IRentService;
use Illuminate\Http\JsonResponse;

class RentController extends Controller
{
    public function __construct(
        private readonly IRentService $rentService
    ) {}

    public function index(): JsonResponse
    {
        return $this->rentService->getRents();
    }

    public function show(Rent $rent): JsonResponse
    {
        return $this->rentService->getRent($rent);
    }

    public function store(StoreRentRequest $request): JsonResponse
    {
        return $this->rentService->createRent($request);
    }

    public function update(UpdateRentRequest $request, Rent $rent): JsonResponse
    {
        return $this->rentService->updateRent($request, $rent);
    }

    public function destroy(Rent $rent): JsonResponse
    {
        return $this->rentService->deleteRent($rent);
    }
}
