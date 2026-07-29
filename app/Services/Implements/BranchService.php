<?php

namespace App\Services\Implements;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Company;
use App\Repositories\IBranchRepository;
use App\Services\IBranchService;
use App\Services\IImageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\LogBatch;
use Throwable;

class BranchService implements IBranchService
{
    public function __construct(
        private readonly IBranchRepository $branchRepository,
        private readonly IImageService $imageService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $branches = $this->branchRepository->findAll();

            return response()->json([
                'status' => true,
                'data' => BranchResource::collection($branches),
            ]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $branch = $this->branchRepository->findById($id);

            if (! $branch) {
                return response()->json(['status' => false, 'message' => __('branch.not_found')], 404);
            }

            return response()->json(['status' => true, 'data' => new BranchResource($branch)]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function store(StoreBranchRequest $request): JsonResponse
    {
        return $this->saveBranch(null, $request->all());
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, UpdateBranchRequest $request): JsonResponse
    {
        $branch = $this->branchRepository->findById($id);

        if (! $branch) {
            return response()->json(['status' => false, 'message' => __('branch.not_found')], 404);
        }

        return $this->saveBranch($branch, $request->all());
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $branch = $this->branchRepository->findById($id);

            if (! $branch) {
                return response()->json(['status' => false, 'message' => __('branch.not_found')], 404);
            }

            if ($branch->isHeadquarters()) {
                return response()->json(['status' => false, 'message' => __('branch.cannot_deactivate_headquarters')], 422);
            }

            $this->branchRepository->deactivate($branch);

            return response()->json(['status' => true, 'message' => [__('branch.deactivated')]]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    public function switch(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $companyId = $request->input('company_id');

            $branch = $this->branchRepository->findById($companyId);

            if (! $branch) {
                return response()->json(['status' => false, 'message' => __('branch.not_found')], 404);
            }

            if (! $user->can('companies.view_all')) {
                $hasAccess = $user->companies()->where('companies.id', $companyId)->exists();

                if (! $hasAccess) {
                    return response()->json(['status' => false, 'message' => __('branch.no_access')], 403);
                }
            }

            $accessible = $this->branchRepository->findAccessibleForUser($user);

            return response()->json([
                'status' => true,
                'message' => [__('branch.switched')],
                'data' => [
                    'current_company_id' => $companyId,
                    'accessible_branches' => BranchResource::collection($accessible),
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Throwable
     */
    private function saveBranch(?Company $branch, array $data): JsonResponse
    {
        LogBatch::startBatch();
        DB::beginTransaction();

        try {
            $isNew = ! $branch;
            $branchData = $data['branch'] ?? [];

            if ($isNew) {
                $headquarters = $this->branchRepository->findHeadquarters();
                $branchData['parent_company_id'] = $branchData['parent_company_id'] ?? $headquarters?->id;
                $branch = $this->branchRepository->create($branchData);
            } else {
                $branch = $this->branchRepository->update($branch, $branchData);
            }

            if (array_key_exists('contacts', $data)) {
                $branch->syncHasMany('contacts', $data['contacts'] ?? []);
            }

            if (array_key_exists('addresses', $data)) {
                $branch->syncHasMany('addresses', $data['addresses'] ?? []);
            }

            if (array_key_exists('logo_image_id', $branchData)) {
                $images = $branchData['logo_image_id']
                    ? [['id' => $branchData['logo_image_id'], 'is_cover' => true, 'sort_order' => 0]]
                    : [];
                $this->imageService->syncImages($branch, $images);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [$isNew ? __('branch.created') : __('branch.updated')],
            ], $isNew ? 201 : 200);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $exception->getMessage()], 400);
        } finally {
            LogBatch::endBatch();
        }
    }
}
