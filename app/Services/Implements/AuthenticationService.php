<?php

namespace App\Services\Implements;

use App\Http\Requests\AuthenticationRequest;
use App\Models\User;
use App\Services\IAuthenticationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationService implements IAuthenticationService
{
    /**
     * Get a JWT via given credentials.
     *
     * @throws ValidationException
     */
    public function login(AuthenticationRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->input('remember_me', false);

        try {
            $ttl = $rememberMe ? 7200 : (int) config('jwt.ttl');
            auth('api')->factory()->setTTL($ttl);

            $contextClaims = [
                'tid' => tenant()?->getTenantKey() ?? 'central',
                'sess_ttl' => $ttl,
            ];

            if (! $token = JWTAuth::claims($contextClaims)->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => [__('auth.failed')],
                ], 401);
            }

            $tokenExpiresIn = auth('api')->factory()->getTTL() * 60; // 1h

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => [__('auth.token')],
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $this->getUserWithPermissions(auth('api')->user()),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $tokenExpiresIn,
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user === null) {
            return response()->json([
                'status' => false,
                'message' => [__('auth.failed')],
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $this->getUserWithPermissions($user),
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => true,
            'message' => [__('auth.logout')],
        ], 200);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        $token = JWTAuth::getToken();

        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => [__('auth.unauthenticated')],
            ], 401);
        }

        // setRefreshFlow ignores exp, only validates refresh_ttl window
        $payload = JWTAuth::manager()->setRefreshFlow()->decode($token);

        $expectedTid = tenant()?->getTenantKey() ?? 'central';
        if ($payload->get('tid', 'central') !== $expectedTid) {
            return response()->json([
                'status' => false,
                'message' => [__('auth.unauthenticated')],
            ], 401);
        }

        auth('api')->factory()->setTTL($payload->get('sess_ttl', (int) config('jwt.ttl')));

        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function sendResetEmail(Request $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => __($status),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => __($status),
            ], 400);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => __('auth.password_reset'),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => __($status),
            ], 400);
        }
    }

    private function getUserWithPermissions(User $user): array
    {
        $user->loadMissing('roles');

        return [
            'id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->values()->all(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
        ];
    }
}
