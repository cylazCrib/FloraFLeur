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
        // 1. Log the user in
        $request->authenticate();

        // 2. Regenerate session
        $request->session()->regenerate();

        // 3. Get the user
        $user = Auth::user();

        // 4. Determine redirect based on role
        $redirectUrl = route('customer.dashboard'); // Default
        $message = 'Login successful!';

        if ($user->role === 'admin') {
            $redirectUrl = route('admin.dashboard');
            $message = 'Admin login successful! Redirecting...';
        } 
        elseif ($user->role === 'vendor') {
            // Check checkbox for vendors
            if ($request->has('login_as_vendor') || $request->input('login_as_vendor') == 'on') {
                $redirectUrl = route('vendor.dashboard');
                $message = 'Vendor login successful! Redirecting...';
            } else {
                // Vendor forgot to check the box -> Log them out
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'To log in as a vendor, please check the "Log in as Vendor" box.',
                        'errors' => ['login_as_vendor' => ['Please check the vendor box.']]
                    ], 422);
                }
                return back()->withErrors(['email' => 'To log in as a vendor, please check the "Log in as Vendor" box.']);
            }
        }

        // 5. Return JSON response (for your Landing Page Modals)
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl
            ]);
        }

        // 6. Fallback for normal form submits
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