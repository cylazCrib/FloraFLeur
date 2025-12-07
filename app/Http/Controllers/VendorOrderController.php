<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User; // For drivers

class VendorOrderController extends Controller
{
    public function index()
    {
        // 1. Get the logged-in Vendor's Shop
        $shopId = Auth::user()->shop->id;

        // 2. Fetch Orders for THIS shop only
        $orders = Order::with(['items', 'user']) // Eager load items and customer info
                    ->where('shop_id', $shopId)
                    ->latest()
                    ->get();

        // 3. Fetch Drivers (Users with role 'driver' or staff)
        // Adjust this query based on how you store drivers
        $drivers = User::where('role', 'driver')->get(); 

        return view('vendor.orders', compact('orders', 'drivers'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Security: Ensure vendor owns this order
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => "Order #{$order->order_number} updated to {$request->status}"
        ]);
    }

    public function assignDriver(Request $request, Order $order)
    {
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->update(['driver_name' => $request->driver_name]);

        return response()->json([
            'message' => "Driver assigned successfully."
        ]);
    }
}