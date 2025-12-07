<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\OrderItem;

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) return view('vendor.dashboard');

        // --- 1. STATISTICS ---
        $totalSales = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->sum('total_amount');
        $totalOrders = Order::where('shop_id', $shop->id)->count();
        $pendingOrders = Order::where('shop_id', $shop->id)->where('status', 'Pending')->count();
        $deliveredOrders = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->count();
        
        $inventoryCount = InventoryItem::where('shop_id', $shop->id)->count();
        $lowStockCount = InventoryItem::where('shop_id', $shop->id)->where('quantity', '<=', 5)->count();

        // --- 2. DATA LISTS ---
        $recentOrders = Order::with('items')->where('shop_id', $shop->id)->latest()->take(5)->get();
        $orders = Order::with(['items', 'user'])->where('shop_id', $shop->id)->latest()->get();
        $products = $shop->products()->latest()->get();
        
        // Inventory Split
        $inventory = InventoryItem::where('shop_id', $shop->id)->latest()->get();
        $items = $inventory->where('type', 'item');
        $flowers = $inventory->where('type', 'flower');
        
        // Staff (renamed to $staff to be consistent)
        $staff = User::where('shop_id', $shop->id)->where('id', '!=', $user->id)->get();
        
        // Drivers
        $drivers = User::where('shop_id', $shop->id)->where('role', 'Driver')->get();

        return view('vendor.dashboard', compact(
            'totalSales', 'totalOrders', 'pendingOrders', 'deliveredOrders',
            'inventoryCount', 'lowStockCount', 'recentOrders',
            'orders', 'products', 'inventory', 'items', 'flowers', 'staff', 'drivers'
        ));
    }

    // --- ACTIONS ---

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
        InventoryItem::where('id', $id)->delete();
        return response()->json(['message' => 'Item removed']);
    }

    public function storeStaff(Request $request) {
        $data = $request->except(['staff_id']);
        
        if($request->staff_id) {
            $user = User::find($request->staff_id);
            $user->update($request->only(['name', 'email', 'phone', 'role']));
        } else {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('password123'),
                'role' => $request->role,
                'shop_id' => Auth::user()->shop->id,
                'status' => 'Active'
            ]);
        }
        return response()->json(['message' => 'Staff saved']);
    }

    public function destroyStaff($id) {
        User::where('id', $id)->delete();
        return response()->json(['message' => 'Staff removed']);
    }

    public function updateOrderStatus(Request $request, Order $order) {
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }
    public function assignDriver(Request $request, Order $order) {
        $order->update(['driver_name' => $request->driver_name]);
        return response()->json(['message' => 'Driver assigned']);
    }
    public function storeProduct(Request $request) { 
        $data = $request->except(['product_id', 'image']);
        if($request->hasFile('image')) $data['image'] = $request->file('image')->store('products', 'public');
        
        if($request->product_id) { Product::find($request->product_id)->update($data); }
        else { Auth::user()->shop->products()->create($data); }
        return response()->json(['message' => 'Product saved']); 
    }
    public function destroyProduct($id) { Product::destroy($id); return response()->json(['message' => 'Deleted']); }
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