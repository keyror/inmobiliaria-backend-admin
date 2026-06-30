<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $jwtToken = JWTAuth::parseToken();
            $jwtToken->authenticate();

            $tid = $jwtToken->getPayload()->get('tid');
            $expectedTid = tenant()?->getTenantKey() ?? 'central';

            if ($tid !== $expectedTid) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
