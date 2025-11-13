<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthenticated.');
        }

        if (!$request->user()->hasRole($role)) {
            abort(403, 'Unauthorized. This action requires the ' . $role . ' role.');
        }

        if (!$request->user()->is_active) {
            abort(403, 'Your account is inactive. Please contact an administrator.');
        }

        return $next($request);
    }
}
