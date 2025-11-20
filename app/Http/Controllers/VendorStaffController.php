<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff;

class VendorStaffController extends Controller
{
    /**
     * READ: Show the staff management page.
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        if (!$shop) return redirect()->route('vendor.dashboard');

        // Get all staff for this shop, ordered by newest
        $staffMembers = $shop->staff()->latest()->get();

        return view('vendor.staff', compact('staffMembers'));
    }

    /**
     * CREATE: Add a new staff member.
     */
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|string',
        ]);

        // Save to database linked to the current shop
        Auth::user()->shop->staff()->create($validated);

        return response()->json(['message' => 'Staff member added successfully!']);
    }

    /**
     * UPDATE: Edit an existing staff member.
     */
    public function update(Request $request, Staff $staff)
    {
        // Security Check: Ensure this staff belongs to this shop
        if ($staff->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|string',
        ]);

        $staff->update($validated);

        return response()->json(['message' => 'Staff details updated!']);
    }

    /**
     * UPDATE STATUS: Suspend or Activate a staff member.
     */
    public function toggleStatus(Staff $staff)
    {
        if ($staff->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Toggle between 'Active' and 'Suspended'
        $newStatus = ($staff->status === 'Active') ? 'Suspended' : 'Active';
        $staff->update(['status' => $newStatus]);

        return response()->json(['message' => "Staff marked as $newStatus."]);
    }
}