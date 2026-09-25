<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage: ->middleware('role:super_admin,admin')
     * Uses session/file storage without a Redis dependency.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is disabled.');
        }

        // super_admin bypasses all checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403, 'You do not have permission to access this area. Required: '.implode(', ', $roles));
        }

        return $next($request);
    }
}
