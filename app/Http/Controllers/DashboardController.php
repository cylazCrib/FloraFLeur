<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $role = strtolower($user->role ?? '');
        
        // Use a match statement for cleaner, faster execution
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            
            'vendor', 'staff' => $this->handleBusinessRedirect($user),
            
            'customer' => redirect()->route('customer.dashboard'),
            
            default => redirect()->route('landing'),
        };
    }

    private function handleBusinessRedirect($user): RedirectResponse
    {
        $shop = $user->shop; // This calls the getShopAttribute() above

        // If there is NO shop record at all, they definitely aren't approved
        if (!$shop) {
            \Log::error("User {$user->email} has business role but no shop record found.");
            return redirect()->route('landing')->with('status', 'Shop profile not found. Please contact support.');
        }

        $status = strtolower($shop->status ?? 'pending');

        if (in_array($status, ['approved', 'active'])) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('landing')->with('status', 'Your shop access is pending approval.');
    }
}