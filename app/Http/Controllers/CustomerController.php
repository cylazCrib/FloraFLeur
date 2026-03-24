<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomRequest;
use App\Models\Shop;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function dashboard()
{
    // 1. Products (Shop Grid)
    $products = Product::latest()->get()->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'description' => $p->description,
        'price' => (float)$p->price, 
        'image' => Storage::url($p->image),
        'category' => $p->category,
        'occasion' => $p->occasion
    ]);
    
    // 2. Standard Orders (With Eager Loaded Items)
    $orders = Order::with('items')
        ->where('user_id', Auth::id())
        ->latest()
        ->get()
        ->map(function($order) {
            return [
                'id' => $order->id,
                'type' => 'order',
                'order_number' => $order->order_number ?? 'ORD-'.$order->id,
                'status' => $order->status,
                'total_amount' => (float)$order->total_amount,
                'driver_name' => $order->driver_name,
                'created_at' => $order->created_at->format('M d, Y'),
                'items' => $order->items->map(fn($item) => [
                    'name' => $item->product_name,
                    'price' => (float)$item->price,
                    'quantity' => (int)$item->quantity,
                    'image' => $item->image ? Storage::url($item->image) : null,
                ]),
            ];
        });

    // 3. Custom Requests (Handled as Orders for the UI)
    $requests = CustomRequest::where('user_id', Auth::id())
        ->latest()
        ->get()
        ->map(function($req) {
            return [
                'id' => $req->id,
                'type' => 'request',
                'order_number' => 'REQ-'.$req->id,
                'status' => $req->status,
                'description' => $req->description,
                // If florist quoted a price, use it; otherwise use customer budget
                'total_amount' => (float)($req->vendor_quote ?? $req->budget),
                'driver_name' => $req->driver_name ?? null,
                'created_at' => $req->created_at->format('M d, Y'),
                'items' => [], // Custom requests don't have standard items usually
            ];
        });

    return Inertia::render('Customer/Dashboard', [
        'products' => $products,
        'orders' => $orders,
        'requests' => $requests,
        'shops' => Shop::where('status', 'approved')->get(),
        'occasions' => ['Birthday', 'Anniversary', 'Valentines', 'Graduation'],
        'user' => Auth::user()
    ]);
}
}