<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\InventoryItem;

class VendorController extends Controller
{
    public function dashboard()
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            // Fallback if no shop is linked yet
            return view('vendor.dashboard', [
                'totalSales' => 0,
                'totalOrders' => 0,
                'pendingOrders' => 0,
                'deliveredOrders' => 0,
                'inventoryCount' => 0,
                'lowStockCount' => 0,
                'recentOrders' => []
            ]);
        }

        // 1. Calculate Statistics
        $totalSales = $shop->orders()->where('status', 'Delivered')->sum('total_amount');
        $totalOrders = $shop->orders()->count();
        $pendingOrders = $shop->orders()->where('status', 'Pending')->count();
        $deliveredOrders = $shop->orders()->where('status', 'Delivered')->count();
        
        // 2. Inventory Stats
        $inventoryCount = $shop->inventory()->count(); // Total unique items
        $lowStockCount = $shop->inventory()->where('quantity', '<=', 5)->count();

        // 3. Fetch Recent Orders (Limit to 5)
        $recentOrders = $shop->orders()->with('items')->latest()->take(5)->get();

        return view('vendor.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'pendingOrders', 
            'deliveredOrders', 
            'inventoryCount', 
            'lowStockCount', 
            'recentOrders'
        ));
    }

    public function gmail()
    {
        return view('vendor.gmail');
    }

    public function connectGmail(Request $request)
    {
        $request->validate([
            'gmail_email' => 'required|email',
            'app_password' => 'required|string',
        ]);

        return response()->json(['message' => 'Gmail account connected successfully!']);
    }

    public function settings()
    {
        return view('vendor.settings');
    }

    public function postAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|string',
        ]);

        // Here you would save the announcement to the database
        
        return response()->json(['message' => 'Announcement posted to ' . $request->target . '!']);
    }

    public function generateReport(Request $request)
    {
        return response()->json(['message' => 'Generating PDF report... Download will start shortly.']);
    }
}