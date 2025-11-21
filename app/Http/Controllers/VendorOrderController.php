<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class VendorOrderController extends Controller
{
    /**
     * READ: Show the orders page.
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        if (!$shop) return redirect()->route('vendor.dashboard');

        // 1. Get orders
        $orders = $shop->orders()->with(['items', 'customer'])->latest()->get();
        
        // 2. Get products (for Add Order)
        $products = $shop->products()->get();
        
        // 3. Get drivers (for Assign Driver)
        $drivers = $shop->staff()->where('role', 'Driver')->where('status', 'Active')->get();

        // 4. NEW: Get Notification History for this shop's orders
        // We find notifications where the related order belongs to this shop
        $notifications = \App\Models\OrderNotification::whereHas('order', function($q) use ($shop) {
            $q->where('shop_id', $shop->id);
        })->with('order')->latest()->get();

        return view('vendor.orders', compact('orders', 'products', 'drivers', 'notifications'));
    }
    /**
     * CREATE: Manually create a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'delivery_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $shop = Auth::user()->shop;
        $product = Product::find($request->product_id);

        // Create the Order
        $order = $shop->orders()->create([
            'user_id' => Auth::id(), // Linked to the vendor for manual orders
            'order_number' => 'FF-' . rand(10000, 99999),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => null,
            'delivery_address' => $request->delivery_address,
            'delivery_date' => $request->delivery_date,
            'total_amount' => $product->price * $request->quantity,
            'status' => 'Pending'
        ]);

        // Create the Order Item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $request->quantity,
            'price' => $product->price
        ]);

        return response()->json(['message' => 'Order created successfully!']);
    }

    /**
     * UPDATE: Change the status of an order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:Pending,Being Made,Delivered,Canceled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Order status updated!']);
    }

    /**
     * READ: Fetch a single order's full details.
     */
    public function show(Order $order)
    {
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $order->load('items');
        return response()->json($order);
    }

    /**
     * UPDATE: Assign a driver to an order.
     */
    public function assignDriver(Request $request, Order $order)
    {
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'driver_name' => 'required|string',
        ]);

        $order->update(['driver_name' => $request->driver_name]);

        return response()->json(['message' => 'Driver assigned successfully!']);
    }

    /**
     * CREATE: Send a notification for an order.
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'type'     => 'required|string',
            'message'  => 'required|string',
        ]);

        // Verify ownership
        $order = Order::find($request->order_id);
        if ($order->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Save to database
        $order->notifications()->create([
            'type' => $request->type,
            'message' => $request->message,
            'status' => 'Sent'
        ]);

        return response()->json(['message' => 'Notification sent successfully!']);
    }
}