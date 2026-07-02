<?php

namespace App\Services\Implements;

use App\Models\Tenant;
use App\Repositories\ITenantUserRepository;
use App\Services\ITenantUserService;
use App\Validation\UserRules;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantUserService implements ITenantUserService
{
    public function __construct(
        private readonly ITenantUserRepository $tenantUserRepository
    ) {}

    public function index(Tenant $tenant): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $users = $this->tenantUserRepository->getUsers();

            return response()->json(['status' => true, 'data' => $users]);
        } finally {
            tenancy()->end();
        }
    }

    public function show(Tenant $tenant, string $userId): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $user = $this->tenantUserRepository->findUser($userId);

            return response()->json(['status' => true, 'data' => $user]);
        } finally {
            tenancy()->end();
        }
    }

    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $validated = $request->validate(UserRules::store());

            DB::beginTransaction();
            try {
                $data = [
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'status_type_id' => $validated['status_type_id'],
                ];

                $this->tenantUserRepository->createUser($data, $validated['roles']);

                DB::commit();

                return response()->json(['status' => true, 'message' => [__('user.created')]], 201);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
            }
        } finally {
            tenancy()->end();
        }
    }

    public function update(Request $request, Tenant $tenant, string $userId): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $validated = $request->validate(UserRules::update($userId));

            DB::beginTransaction();
            try {
                $data = [
                    'email' => $validated['email'],
                    'status_type_id' => $validated['status_type_id'],
                ];

                if (! empty($validated['password'])) {
                    $data['password'] = $validated['password'];
                }

                $this->tenantUserRepository->updateUser($userId, $data, $validated['roles']);

                DB::commit();

                return response()->json(['status' => true, 'message' => [__('user.updated')]]);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
            }
        } finally {
            tenancy()->end();
        }
    }

    public function destroy(Tenant $tenant, string $userId): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            DB::beginTransaction();
            try {
                $this->tenantUserRepository->deleteUser($userId);

                DB::commit();

                return response()->json(['status' => true, 'message' => [__('user.deleted')]]);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
            }
        } finally {
            tenancy()->end();
        }
    }

    public function roles(Tenant $tenant): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $roles = $this->tenantUserRepository->getRoles();

            return response()->json(['status' => true, 'data' => $roles]);
        } finally {
            tenancy()->end();
        }
    }

    public function statuses(Tenant $tenant): JsonResponse
    {
        tenancy()->initialize($tenant);
        try {
            $statuses = $this->tenantUserRepository->getStatuses();

            return response()->json(['status' => true, 'data' => $statuses]);
        } finally {
            tenancy()->end();
        }
    }
}
