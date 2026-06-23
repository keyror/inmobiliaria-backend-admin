<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = auth('api')->user();

        if ($user === null) {
            return response()->json([
                'status' => false,
                'message' => 'No autenticado.',
            ], 401);
        }

        $requiredPermissions = $this->normalizePermissions($permissions);

        if ($requiredPermissions !== [] && ! $user->hasAnyPermission($requiredPermissions)) {
            return $this->forbiddenResponse($requiredPermissions);
        }

        return $next($request);
    }

    /**
     * @param  array<int, string>  $permissions
     * @return array<int, string>
     */
    private function normalizePermissions(array $permissions): array
    {
        return collect($permissions)
            ->flatMap(static fn (string $permission): array => preg_split('/[|,]/', $permission) ?: [])
            ->map(static fn (string $permission): string => trim($permission))
            ->filter(static fn (string $permission): bool => $permission !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $requiredPermissions
     */
    private function forbiddenResponse(array $requiredPermissions): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'No tienes permisos para acceder a este recurso.',
            'required_permissions' => $requiredPermissions,
        ], 403);
    }
}
