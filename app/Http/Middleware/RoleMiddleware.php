<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtolower($user->role ?? '');

        // Normalize requested roles to lowercase
        $roles = array_map('strtolower', $roles);

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // If user is not in the allowed roles, redirect to their own dashboard
        return redirect()->route('dashboard');
    }
}
