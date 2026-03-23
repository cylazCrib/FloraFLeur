<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $role = Auth::user()->role ?? null;

        return match ($role) {
            'vendor' => redirect()->route('vendor.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('landing'),
        };
    }
}