<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // If roles are provided and not '*', check if the user role is allowed
            if (!empty($roles) && $roles[0] !== '*' && !in_array($user->role, $roles, true)) {
                return ResponseHelper::error('Forbidden: You do not have access to this resource.', 403);
            }
        } catch (JWTException $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Invalid token', 401);
        }

        return $next($request);
    }
}
