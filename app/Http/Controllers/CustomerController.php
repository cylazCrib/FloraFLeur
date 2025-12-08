<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CustomerController extends Controller
{
   public function dashboard()
    {
        // [FIX] Ensure occasion is selected
        $products = Product::with('shop')->latest()->get();
        
        $orders = Order::with('items')->where('user_id', Auth::id())->latest()->get();
        
        return view('customer.dashboard', compact('products', 'orders'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'payment_method' => 'required|string'
        ]);

        $user = Auth::user();
        $cartItems = $request->items;
        $ordersByShop = [];

        foreach ($cartItems as $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            $shopId = $product->shop_id;
            
            if (!isset($ordersByShop[$shopId])) {
                $ordersByShop[$shopId] = ['shop_id' => $shopId, 'items' => [], 'total' => 0];
            }

            // [FIX] Clean price and cast to float
            $cleanPrice = (float) str_replace([',', '₱'], '', $product->price);
            $qty = (int) $item['qty'];

            $ordersByShop[$shopId]['items'][] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $cleanPrice
            ];
            
            // [FIX] Safe multiplication
            $ordersByShop[$shopId]['total'] += $cleanPrice * $qty;
        }

        DB::beginTransaction();
        try {
            foreach ($ordersByShop as $shopData) {
                $order = Order::create([
                    'shop_id' => $shopData['shop_id'],
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'customer_name' => $user->name,
                    'customer_phone' => '09123456789', // Placeholder
                    'customer_email' => $user->email,
                    'delivery_address' => 'Default Address',
                    'delivery_date' => now()->addDays(3),
                    'total_amount' => $shopData['total'],
                    'status' => 'Pending',
                    'payment_method' => $request->payment_method
                ]);

                foreach ($shopData['items'] as $i) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $i['product']->id,
                        'product_name' => $i['product']->name,
                        'quantity' => $i['qty'],
                        'price' => $i['price'],
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Order placed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}