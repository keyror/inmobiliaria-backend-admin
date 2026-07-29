<?php

namespace App\Services;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface IBranchService
{
    public function index(): JsonResponse;

    public function show(string $id): JsonResponse;

    public function store(StoreBranchRequest $request): JsonResponse;

    public function update(string $id, UpdateBranchRequest $request): JsonResponse;

    public function destroy(string $id): JsonResponse;

    public function switch(Request $request): JsonResponse;
}
