<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin can access everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if user has the required permission
        if ($user->permissions && in_array($permission, $user->permissions)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this area.');
    }
}
