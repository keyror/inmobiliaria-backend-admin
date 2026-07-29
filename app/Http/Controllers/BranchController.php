<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Services\IBranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        private readonly IBranchService $branchService
    ) {}

    public function index(): JsonResponse
    {
        return $this->branchService->index();
    }

    public function show(string $id): JsonResponse
    {
        return $this->branchService->show($id);
    }

    public function store(StoreBranchRequest $request): JsonResponse
    {
        return $this->branchService->store($request);
    }

    public function update(string $id, UpdateBranchRequest $request): JsonResponse
    {
        return $this->branchService->update($id, $request);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->branchService->destroy($id);
    }

    public function switch(Request $request): JsonResponse
    {
        return $this->branchService->switch($request);
    }
}
