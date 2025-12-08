<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\OrderItem;
use App\Models\Staff; // <--- NEW MODEL

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) return view('vendor.dashboard');

        // 1. STATS
        $totalSales = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->sum('total_amount');
        $totalOrders = Order::where('shop_id', $shop->id)->count();
        $inventoryCount = InventoryItem::where('shop_id', $shop->id)->count();
        $lowStockCount = InventoryItem::where('shop_id', $shop->id)->where('quantity', '<=', 5)->count();
        $pendingOrders = Order::where('shop_id', $shop->id)->where('status', 'Pending')->count();
        $deliveredOrders = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->count();

        // 2. DATA LISTS
        $recentOrders = Order::with('items')->where('shop_id', $shop->id)->latest()->take(5)->get();
        $orders = Order::with(['items', 'user'])->where('shop_id', $shop->id)->latest()->get();
        $products = $shop->products()->latest()->get();
        
        $inventory = InventoryItem::where('shop_id', $shop->id)->latest()->get();
        $items = $inventory->where('type', 'item');
        $flowers = $inventory->where('type', 'flower');
        
        // [FIX] Fetch from Staff table instead of Users
        $staff = Staff::where('shop_id', $shop->id)->latest()->get();
        
        // [FIX] Fetch Drivers from Staff table (where role is Driver)
        $drivers = Staff::where('shop_id', $shop->id)->where('role', 'Driver')->get();

        return view('vendor.dashboard', compact(
            'totalSales', 'totalOrders', 'pendingOrders', 'deliveredOrders',
            'inventoryCount', 'lowStockCount', 'recentOrders',
            'orders', 'products', 'items', 'flowers', 'staff', 'drivers'
        ));
    }

    // --- STAFF ACTIONS (UPDATED) ---

    public function storeStaff(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'phone' => 'required'
        ]);

        // Fix: Use except to remove ID and avoid "Unknown column" error
        $data = $request->except(['staff_id']);
        
        // Ensure shop_id is set
        $data['shop_id'] = Auth::user()->shop->id;
        $data['status'] = $data['status'] ?? 'Active';

        if($request->staff_id) {
            // Update existing Staff record
            $staff = Staff::where('id', $request->staff_id)->where('shop_id', Auth::user()->shop->id)->firstOrFail();
            $staff->update($data);
        } else {
            // Create new Staff record
            Staff::create($data);
        }
        return response()->json(['message' => 'Staff saved successfully']);
    }

    public function destroyStaff($id) {
        Staff::where('id', $id)->where('shop_id', Auth::user()->shop->id)->delete();
        return response()->json(['message' => 'Staff removed']);
    }

    // --- INVENTORY ACTIONS ---
    public function storeInventory(Request $request) {
        $data = $request->except(['item_id']);
        if($request->item_id) {
            InventoryItem::where('id', $request->item_id)->where('shop_id', Auth::user()->shop->id)->update($data);
        } else {
            Auth::user()->shop->inventory()->create($data);
        }
        return response()->json(['message' => 'Inventory saved']);
    }

    public function destroyInventory($id) {
        InventoryItem::destroy($id);
        return response()->json(['message' => 'Item removed']);
    }

    // --- PRODUCT ACTIONS ---
    public function storeProduct(Request $request) {
        $data = $request->except(['product_id', 'image']);
        if($request->hasFile('image')) $data['image'] = $request->file('image')->store('products', 'public');
        
        if($request->product_id) { Product::find($request->product_id)->update($data); }
        else { Auth::user()->shop->products()->create($data); }
        return response()->json(['message' => 'Product saved']);
    }
    public function destroyProduct($id) { Product::destroy($id); return response()->json(['message' => 'Deleted']); }

    // --- ORDER ACTIONS ---
    public function updateOrderStatus(Request $request, Order $order) {
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }
    public function assignDriver(Request $request, Order $order) {
        $order->update(['driver_name' => $request->driver_name]);
        return response()->json(['message' => 'Driver assigned']);
    }
    public function storeManualOrder(Request $request) {
        $order = Order::create([
            'shop_id' => Auth::user()->shop->id, 'user_id' => Auth::id(), 'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_name' => $request->customer_name, 'customer_phone' => $request->customer_phone,
            'delivery_address' => $request->delivery_address, 'total_amount' => $request->total_amount,
            'status' => 'Pending', 'payment_method' => $request->payment_method
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => 0, 'product_name' => $request->product_name, 'quantity' => 1, 'price' => $request->total_amount]);
        return response()->json(['message' => 'Order created']);
    }
}