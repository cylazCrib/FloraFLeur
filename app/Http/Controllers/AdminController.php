<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. STATS (Case insensitive checks)
        $stats = [
            'active_vendors' => Shop::whereIn('status', ['Active', 'active', 'approved', 'Approved'])->count(),
            'pending_shops'  => Shop::whereIn('status', ['Pending', 'pending'])->count(),
            'total_users'    => User::where('role', 'customer')->count(),
            'total_products' => Product::count()
        ];

        // 2. LISTS
        // Catches 'pending' and 'Pending'
        $pendingShops = Shop::with('user')
            ->whereIn('status', ['Pending', 'pending'])
            ->latest()
            ->get();
        
        // Catches all active variations
        $activeShops = Shop::with('user')
            ->whereIn('status', ['Active', 'active', 'approved', 'Approved', 'Suspended', 'suspended'])
            ->latest()
            ->get();
        
        // Owners for Activity Log
        $owners = Shop::with('user')
            ->whereIn('status', ['Active', 'active', 'approved', 'Approved', 'Suspended', 'suspended'])
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingShops', 'activeShops', 'owners'));
    }

    // --- AJAX ACTIONS ---

    public function getOwnerActivity($id)
    {
        try {
            $shop = Shop::findOrFail($id);
            
            // Safe Data Retrieval using optional() to prevent crashes
            $orders = Order::where('shop_id', $id)->latest()->take(5)->get()->map(fn($o) => [
                'type' => 'order',
                'text' => "Order #{$o->order_number} - ₱" . number_format($o->total_amount),
                'date' => optional($o->created_at)->format('M d, H:i') ?? 'N/A',
                'status' => $o->status
            ]);

            $products = Product::where('shop_id', $id)->latest()->take(5)->get()->map(fn($p) => [
                'type' => 'product',
                'text' => "Added: {$p->name}",
                'date' => optional($p->created_at)->format('M d, H:i') ?? 'N/A',
                'status' => 'Qty: ' . $p->quantity
            ]);

            // Always add a "Joined" event so list is never empty
            $joined = collect([[
                'type' => 'system',
                'text' => "Shop Registered",
                'date' => optional($shop->created_at)->format('M d, H:i') ?? 'N/A',
                'status' => 'Success'
            ]]);

            $activity = $orders->merge($products)->merge($joined)->sortByDesc('date')->values();

            return response()->json([
                'shop' => $shop->name,
                'owner' => optional($shop->user)->name ?? 'Unknown',
                'activity' => $activity
            ]);

        } catch (\Exception $e) {
            // Return JSON error, don't crash with HTML
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function approveShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'approved']); 
        if($shop->user) $shop->user->update(['role' => 'vendor']);
        return response()->json(['message' => 'Shop approved']);
    }

    public function rejectShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();
        return response()->json(['message' => 'Shop rejected']);
    }

    public function toggleShopStatus($id)
    {
        $shop = Shop::findOrFail($id);
        $isActive = in_array(strtolower($shop->status), ['active', 'approved']);
        $newStatus = $isActive ? 'Suspended' : 'approved';
        $shop->update(['status' => $newStatus]);
        return response()->json(['message' => "Shop marked as $newStatus"]);
    }
}