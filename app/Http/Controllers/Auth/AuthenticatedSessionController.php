<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // 1. Attempt login
        $request->authenticate();

        // 2. Regenerate session (Security)
        $request->session()->regenerate();

        // 3. Get user and determine redirect URL (Using PATHS to prevent 500 errors)
        $user = Auth::user();
        $redirectUrl = '/customer/dashboard'; // Default path
        $message = 'Login successful!';

        if ($user->role === 'admin') {
            $redirectUrl = '/admin/dashboard'; // Explicit path
            $message = 'Admin login successful! Redirecting...';
        } 
        elseif ($user->role === 'vendor') {
            // Check the vendor checkbox
            if ($request->has('login_as_vendor') || $request->input('login_as_vendor') == 'on') {
                $redirectUrl = '/vendor/dashboard'; // Explicit path
                $message = 'Vendor login successful! Redirecting...';
            } else {
                // Force logout if checkbox is missing
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Return error
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'To log in as a vendor, please check the "Log in as Vendor" box.'
                    ], 422);
                }
                return back()->withErrors(['email' => 'To log in as a vendor, please check the "Log in as Vendor" box.']);
            }
        }

        // 4. Handle AJAX/Fetch requests (Landing Page Modals)
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl
            ]);
        }

        // 5. Handle Standard Login
        return redirect($redirectUrl);
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