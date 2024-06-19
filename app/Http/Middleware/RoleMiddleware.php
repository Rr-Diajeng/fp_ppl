<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles) {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized.'
                ],
                'data' => null
            ], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'status' => [
                    'code' => 403,
                    'message' => 'Forbidden. You are not allowed to access this resource.'
                ],
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
