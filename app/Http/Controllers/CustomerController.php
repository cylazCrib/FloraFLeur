<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomRequest;

class CustomerController extends Controller
{
    public function dashboard()
    {
        // 1. Fetch products (Latest first)
        $products = Product::latest()->get();
        
        // 2. Eager load items for orders to prevent JS errors
        $orders = Order::with('items')->where('user_id', Auth::id())->latest()->get();
        
        // 3. Fetch custom requests
        $requests = CustomRequest::where('user_id', Auth::id())->latest()->get();

        // 4. [NEW] Define Occasions for the Filter
        $occasions = [
            'Birthday',
            'Anniversary',
            'Valentines',
            'Mothers Day',
            'Graduation',
            'Funeral',
            'Just Because'
        ];

        // 5. Pass everything to the view
        return view('customer.dashboard', compact('products', 'orders', 'requests', 'occasions'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Get the User Model Instance
        
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return response()->json(['message' => 'Profile updated successfully!']);
    }

    public function storeOrder(Request $request)
    {
        $request->validate(['items' => 'required|array', 'payment_method' => 'required']);
        $user = Auth::user();
        
        $cartItems = $request->items;
        $ordersByShop = [];

        // Logic to group orders by Shop
        foreach ($cartItems as $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            $shopId = $product->shop_id;
            
            if (!isset($ordersByShop[$shopId])) {
                $ordersByShop[$shopId] = ['shop_id' => $shopId, 'items' => [], 'total' => 0];
            }

            $cleanPrice = (float) str_replace([',', '₱', ' '], '', $product->price);
            $qty = (int) $item['qty'];

            $ordersByShop[$shopId]['items'][] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $cleanPrice,
                'qty' => $qty
            ];
            
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
                    'customer_phone' => $user->phone ?? 'N/A',
                    'customer_email' => $user->email,
                    'delivery_address' => $user->address ?? 'Default Address',
                    'delivery_date' => now()->addDays(3),
                    'total_amount' => $shopData['total'],
                    'status' => 'Pending',
                    'payment_method' => $request->payment_method
                ]);

                foreach ($shopData['items'] as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'product_name' => $itemData['product_name'],
                        'quantity' => $itemData['qty'],
                        'price' => $itemData['price'],
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

    public function storeRequest(Request $request)
    {
        $request->validate(['description' => 'required']);
        CustomRequest::create([
            'user_id' => Auth::id(),
            'description' => $request->description,
            'budget' => $request->budget,
            'contact_number' => $request->contact_number,
            'status' => 'Pending'
        ]);
        return response()->json(['message' => 'Request sent successfully!']);
    }
}