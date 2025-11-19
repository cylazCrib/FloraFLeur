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
        
        // --- SMART REDIRECT FOR LOGGED-IN USERS ---
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            
            if (!$user) {
                return '/';
            }

            if ($user->role === 'admin') {
                return '/admin/dashboard';
            }
            
            if ($user->role === 'vendor') {
                return '/vendor/dashboard';
            }

            // Default for customers
            return '/customer/dashboard';
        });
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();