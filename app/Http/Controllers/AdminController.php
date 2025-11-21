<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;    // <-- You already have this
use App\Models\Product; // <-- ADD THIS LINE

class AdminController extends Controller
{
    /**
     * Show the main admin dashboard with dynamic data.
     */
    public function dashboard()
    {
        // 1. Get count of all shops with 'approved' status
        $totalShops = Shop::where('status', 'approved')->count();

        // 2. Get count of all shops with 'pending' status
        $newEntries = Shop::where('status', 'pending')->count();

        // 3. Get count of all products in the database
        // This will be 0 until we build the vendor-side product CRUD
        $totalProducts = Product::count(); 

        // Pass these 3 variables to the view
        return view('admin.dashboard', [
            'totalShops' => $totalShops,
            'newEntries' => $newEntries,
            'totalProducts' => $totalProducts
        ]);
    }

    /**
     * READ: Show the 'Manage Registrations' page.
     */
    public function registrations()
    {
        // ... (your existing registrations method) ...
        $pending_shops = Shop::where('status', 'pending')
                                ->with('user')
                                ->get();
        return view('admin.registrations', [
            'pending_shops' => $pending_shops
        ]);
    }

    /**
     * UPDATE: Approve a shop.
     */
    public function approveShop(Shop $shop)
    {
        // 1. Update the shop's status
        $shop->update(['status' => 'approved']);

        // 2. CRITICAL FIX: Update the User's role to 'vendor'
        // This gives them permission to log in to the Vendor Dashboard
        $shop->user->update(['role' => 'vendor']);

        // 3. Redirect back with success message
        return redirect()->route('admin.registrations.index')
            ->with('success', 'Shop approved! The user can now log in as a Vendor.');
    }
    /**
     * DELETE: Reject (and delete) a shop.
     */
    public function rejectShop(Shop $shop)
    {
        // ... (your existing rejectShop method) ...
        $shop->delete();
        return redirect()->route('admin.registrations.index')->with('success', 'Shop has been rejected.');
    }

    public function vendors()
    {
        // Fetch all shops that are NOT pending (i.e., 'approved' or 'suspended')
        $shops = Shop::where('status', '!=', 'pending')
                        ->with('user') // Get the owner's info
                        ->get();

        // Send that data to our new view
        return view('admin.vendors', [
            'shops' => $shops
        ]);
    }

    /**
     * UPDATE: Toggle a shop's status between 'approved' and 'suspended'.
     */
    public function toggleShopStatus(Shop $shop)
    {
        // Check the current status and swap it
        $newStatus = ($shop->status == 'approved') ? 'suspended' : 'approved';

        $shop->update(['status' => $newStatus]);

        // Send the user back to the vendors page with a success message
        return redirect()->route('admin.vendors.index')->with('success', 'Shop status has been updated!');
    }

    public function owners()
{
    // Fetch all shops that are NOT pending (i.e., approved or suspended)
    // We'll need the user (owner) and product count (which we'll add later)
    $shops = Shop::where('status', '!=', 'pending')
                    ->with('user')
                    // ->withCount('products') // We can add this later
                    ->get();

    return view('admin.owners', [
        'shops' => $shops
    ]);
}

/**
 * UPDATE: Handle the 'Send Notification' form.
 */
public function notifyOwners(Request $request)
{
    // Validate the form data
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10',
    ]);

    // ---
    // In the future, this is where you would dispatch a Job 
    // or a Mailable to email all your shop owners.
    // For now, we'll just pretend it worked.
    // ---

    return redirect()->route('admin.owners.index')->with('success', 'Notification sent to all owners!');
}

public function gmail()
{
    return view('admin.gmail');
}

/**
 * READ: Show the 'Settings' page.
 */
public function settings()
{
    return view('admin.settings');
}
}