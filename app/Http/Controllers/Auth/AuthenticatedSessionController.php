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
   /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // 1. Attempt to authenticate the user
        $request->authenticate();

        // 2. Regenerate the session
        $request->session()->regenerate();

        // 3. Get the logged-in user
        $user = Auth::user();

      // --- ADD THIS LINE TEMPORARILY ---
        dd([
            'User Email' => $user->email,
            'User Role' => $user->role,
            'Is Admin?' => ($user->role === 'admin'),
            'Role Length' => strlen($user->role), // Checks for hidden spaces
        ]);
        // ---------------------------------

        $redirectUrl = route('customer.dashboard');
        $message = 'Login successful!';

        if ($user->role === 'admin') {
            $redirectUrl = route('admin.dashboard');
            $message = 'Admin login successful! Redirecting...';
        } 
        elseif ($user->role === 'vendor') {
            // Check if they *intended* to log in as a vendor
            if ($request->has('login_as_vendor')) {
                $redirectUrl = route('vendor.dashboard');
                $message = 'Vendor login successful! Redirecting...';
            } else {
                // They are a vendor but didn't check the box. Log them out and send an error.
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'message' => 'To log in as a vendor, please check the "Log in as Vendor" box.'
                ], 422);
            }
        }

        // For AJAX requests (from our modals)
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl
            ]);
        }

        // --- THIS IS THE IMPORTANT FIX ---
        // Use redirect($redirectUrl) instead of redirect()->intended(...)
        // so it doesn't accidentally send you to the default dashboard.
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
