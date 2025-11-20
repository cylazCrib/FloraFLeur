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

        // Get orders with items/customer
        $orders = $shop->orders()->with(['items', 'customer'])->latest()->get();
        
        // NEW: Get products for the "Add Order" dropdown
        $products = $shop->products()->get();

        return view('vendor.orders', compact('orders', 'products'));
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

        // 1. Create the Order
        $order = $shop->orders()->create([
            'user_id' => Auth::id(), // Linked to the vendor for manual orders
            'order_number' => 'FF-' . rand(10000, 99999),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => null, // Optional for manual orders
            'delivery_address' => $request->delivery_address,
            'delivery_date' => $request->delivery_date,
            'total_amount' => $product->price * $request->quantity,
            'status' => 'Pending'
        ]);

        // 2. Create the Order Item
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
}