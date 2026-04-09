<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // 1. Register the Inertia Middleware
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Disable CSRF for testing environment
        // Detect if we're running tests via PHPUnit or Pest
        if (str_contains(implode(' ', $_SERVER['argv'] ?? []), 'phpunit') || str_contains(implode(' ', $_SERVER['argv'] ?? []), 'pest')) {
             $middleware->validateCsrfTokens(except: ['*']);
        }

        // 2. --- SMART REDIRECT FOR LOGGED-IN USERS ---
        // This ensures that when a user is already logged in and hits the login page, 
        // they are sent to their specific dashboard based on their role.
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            
            if (!$user) {
                return '/';
            }

            $role = strtolower($user->role ?? '');

            if ($role === 'admin') {
                return '/admin/dashboard';
            }
            
            if ($role === 'vendor' || $role === 'staff') {
                return '/vendor/dashboard';
            }

            // Default for customers
            return '/customer/dashboard';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
