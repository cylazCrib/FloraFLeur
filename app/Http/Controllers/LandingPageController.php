<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function show()
    {
        // If logged in AND has no pending status message, send to dashboard
        if (Auth::check()) {
            if (!session('status')) {
                 return redirect()->route('dashboard');
            }
        }

        return Inertia::render('Landing', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'status' => session('status'),
        ]);
    }
}