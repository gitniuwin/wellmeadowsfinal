<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // Check if user role matches any of the allowed roles
        foreach ($roles as $role) {
            // Replace dash back to slash for Personnel/HR Staff
            $role = str_replace('Personnel-HR', 'Personnel/HR', $role);
            if ($userRole === $role) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this page.');
    }
}