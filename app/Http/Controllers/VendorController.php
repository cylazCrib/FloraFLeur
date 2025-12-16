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
use App\Models\CustomRequest;
use App\Models\Staff;

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) return view('vendor.dashboard');

        // --- 1. CALCULATE TOTAL SALES (Orders + Requests) ---
        $orderSales = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->sum('total_amount');
        $requestSales = CustomRequest::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->sum('budget');
        $totalSales = $orderSales + $requestSales;

        // --- 2. OTHER STATS ---
        $totalOrders = Order::where('shop_id', $shop->id)->count();
        $inventoryCount = InventoryItem::where('shop_id', $shop->id)->count();
        $lowStockCount = InventoryItem::where('shop_id', $shop->id)->where('quantity', '<=', 5)->count();
        $pendingOrders = Order::where('shop_id', $shop->id)->where('status', 'Pending')->count();
        $deliveredOrders = Order::where('shop_id', $shop->id)->whereIn('status', ['Delivered', 'Completed'])->count();

        // --- 3. FETCH LISTS ---
        $recentOrders = Order::with('items')->where('shop_id', $shop->id)->latest()->take(5)->get();
        $orders = Order::with(['items', 'user'])->where('shop_id', $shop->id)->latest()->get();
        $products = $shop->products()->latest()->get();
        
        $inventory = InventoryItem::where('shop_id', $shop->id)->latest()->get();
        $items = $inventory->where('type', 'item');
        $flowers = $inventory->where('type', 'flower');
        
        $staff = Staff::where('shop_id', $shop->id)->latest()->get();
        $drivers = Staff::where('shop_id', $shop->id)->where('role', 'Driver')->get();

        $customRequests = CustomRequest::with('user')
            ->where(function($query) {
                $query->where('status', 'Pending')->whereNull('shop_id');
            })
            ->orWhere('shop_id', $shop->id)
            ->latest()
            ->get();

        return view('vendor.dashboard', compact(
            'totalSales', 'totalOrders', 'pendingOrders', 'deliveredOrders',
            'inventoryCount', 'lowStockCount', 'recentOrders',
            'orders', 'products', 'inventory', 'items', 'flowers', 'staff', 'drivers', 'customRequests'
        ));
    }

    // --- [NEW] EXPORT SALES FUNCTION ---
    public function exportSales()
    {
        $shop = Auth::user()->shop;
        if (!$shop) return redirect()->back();

        $filename = "sales-report-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 1. Get Completed Orders
        $orders = Order::where('shop_id', $shop->id)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->get();

        // 2. Get Completed Requests
        $requests = CustomRequest::where('shop_id', $shop->id)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->get();

        $callback = function() use ($orders, $requests) {
            $file = fopen('php://output', 'w');

            // CSV Header Row
            fputcsv($file, ['Date', 'Type', 'ID', 'Customer', 'Items/Description', 'Amount', 'Status']);

            // Rows for Orders
            foreach ($orders as $o) {
                $items = $o->items->map(fn($i) => "{$i->quantity}x {$i->product_name}")->join(', ');
                fputcsv($file, [
                    $o->created_at->format('Y-m-d'),
                    'Order',
                    $o->order_number,
                    $o->customer_name,
                    $items,
                    $o->total_amount,
                    $o->status
                ]);
            }

            // Rows for Requests
            foreach ($requests as $r) {
                fputcsv($file, [
                    $r->created_at->format('Y-m-d'),
                    'Request',
                    'REQ-' . $r->id,
                    $r->user->name ?? 'Guest',
                    $r->description,
                    $r->budget,
                    $r->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // --- EXISTING FUNCTIONS ---

    public function updateRequestStatus(Request $request, $id) {
        $customReq = CustomRequest::findOrFail($id);
        $shopId = Auth::user()->shop->id;

        if ($customReq->shop_id === null) {
            $customReq->shop_id = $shopId;
        } else if ($customReq->shop_id !== $shopId) {
            return response()->json(['message' => 'Error: This request is handled by another vendor.'], 403);
        }

        $customReq->status = $request->status;
        $customReq->save();

        return response()->json(['message' => 'Request updated']);
    }

    public function storeStaff(Request $request) {
        $request->validate(['name'=>'required', 'email'=>'required', 'role'=>'required', 'phone'=>'required']);
        $data = $request->except(['staff_id']);
        $data['shop_id'] = Auth::user()->shop->id;
        $data['status'] = $data['status'] ?? 'Active';

        if($request->staff_id) { Staff::where('id', $request->staff_id)->update($data); }
        else { Staff::create($data); }
        return response()->json(['message' => 'Staff saved']);
    }
    public function destroyStaff($id) { Staff::destroy($id); return response()->json(['message' => 'Removed']); }

    public function storeInventory(Request $request) {
        $data = $request->except(['item_id']);
        if($request->item_id) { InventoryItem::where('id', $request->item_id)->update($data); } 
        else { Auth::user()->shop->inventory()->create($data); }
        return response()->json(['message' => 'Inventory saved']);
    }
    public function destroyInventory($id) { InventoryItem::destroy($id); return response()->json(['message' => 'Item removed']); }
    
    public function storeProduct(Request $request) {
        $data = $request->except(['product_id', 'image']);
        if($request->hasFile('image')) $data['image'] = $request->file('image')->store('products', 'public');
        if($request->product_id) { Product::find($request->product_id)->update($data); }
        else { Auth::user()->shop->products()->create($data); }
        return response()->json(['message' => 'Product saved']);
    }
    public function destroyProduct($id) { Product::destroy($id); return response()->json(['message' => 'Deleted']); }
    
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