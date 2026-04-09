<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user->role; // Assuming 'admin', 'vendor', 'staff', 'customer'

        // 1. Admin Logic
        if ($role === 'admin' || $user->email === 'admin@florafleur.com') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // 2. Vendor / Staff Logic
        if ($role === 'vendor' || $role === 'staff') {
            $shop = $user->shop; // Ensure your User model has: public function shop() { return $this->hasOne(Shop::class); }
            
            // If they are a vendor but the shop isn't approved, send back to landing with message
            if ($role === 'vendor' && (!$shop || !in_array($shop->status, ['approved', 'active']))) {
                return redirect()->route('landing')->with('status', 'Your shop is pending approval.');
            }

            return redirect()->intended(route('vendor.dashboard'));
        }

        // 3. Default (Customer)
        return redirect()->intended(route('customer.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
