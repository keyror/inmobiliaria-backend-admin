<?php

namespace App\Services\Implements;

use App\Http\Requests\AuthenticationRequest;
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
     * @param AuthenticationRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(AuthenticationRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->input('remember_me', false);

        try {
            if ($rememberMe) {
                auth('api')->factory()->setTTL(7200); // 5 días
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => [__('auth.failed')]
                ], 401);
            }

            $tokenExpiresIn = auth('api')->factory()->getTTL() * 60; //1h

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => [__('auth.token')]
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => auth()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $tokenExpiresIn,
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me() :JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => auth()->user(),
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => true,
            'message' => [__('auth.logout')]
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 3
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
                    'password' => Hash::make($password)
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
}
