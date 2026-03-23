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
use App\Models\Shop;
use Inertia\Inertia;

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

        // 4. [NEW] Fetch all approved shops with their products
        $shops = Shop::where('status', 'approved')
                    ->with('products')
                    ->latest()
                    ->get();

        // 5. [NEW] Define Occasions for the Filter
        $occasions = [
            'Birthday',
            'Anniversary',
            'Valentines',
            'Mothers Day',
            'Graduation',
            'Funeral',
            'Just Because'
        ];

        // 6. Get authenticated user
        $user = Auth::user();

        // 7. Pass everything to the view
        return Inertia::render('Customer/Dashboard', compact('products', 'orders', 'requests', 'shops', 'occasions', 'user'));
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
        try {
            \Log::info('🔵 [storeOrder] Order request received', $request->all());
            
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'payment_method' => 'required|string'
            ]);
            
            \Log::info('✅ [storeOrder] Validation passed');
            
            $user = Auth::user();
            $cartItems = $request->items;
            $ordersByShop = [];

            // Logic to group orders by Shop
            foreach ($cartItems as $item) {
                \Log::info('📦 [storeOrder] Processing item:', $item);
                
                $product = Product::find($item['id']);
                if (!$product) {
                    \Log::warning('⚠️ [storeOrder] Product not found:', ['id' => $item['id']]);
                    continue;
                }

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

            \Log::info('🛍️ [storeOrder] Orders grouped by shop:', ['count' => count($ordersByShop)]);

            DB::beginTransaction();
            try {
                foreach ($ordersByShop as $shopData) {
                    \Log::info('📝 [storeOrder] Creating order for shop:', ['shop_id' => $shopData['shop_id']]);
                    
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

                    \Log::info('✅ [storeOrder] Order created:', ['order_id' => $order->id]);

                    foreach ($shopData['items'] as $itemData) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $itemData['product_id'],
                            'product_name' => $itemData['product_name'],
                            'quantity' => $itemData['qty'],
                            'price' => $itemData['price'],
                        ]);
                    }
                    
                    \Log::info('✅ [storeOrder] Order items created for order:', ['order_id' => $order->id]);
                }
                
                DB::commit();
                \Log::info('🎉 [storeOrder] All orders created successfully');
                
                return response()->json([
                    'message' => 'Order placed successfully!',
                    'status' => 'success'
                ], 201);
            } catch (\Exception $dbError) {
                DB::rollBack();
                \Log::error('❌ [storeOrder] Database error:', ['error' => $dbError->getMessage()]);
                throw $dbError;
            }
        } catch (\Exception $e) {
            \Log::error('🔴 [storeOrder] Exception:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'message' => 'Error placing order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'description' => 'required|string',
            'contact_number' => 'required|string',
            'date_needed' => 'required|date_format:Y-m-d\TH:i',
        ]);
        
        CustomRequest::create([
            'user_id' => Auth::id(),
            'shop_id' => $request->shop_id,
            'description' => $request->description,
            'occasion' => $request->occasion,
            'date_needed' => $request->date_needed,
            'budget' => $request->budget,
            'color_preference' => $request->color_preference,
            'contact_number' => $request->contact_number,
            'reference_image_url' => $request->reference_image_url,
            'status' => 'pending'
        ]);
        return response()->json(['message' => 'Request sent to ' . Shop::find($request->shop_id)->name . '! We\'ll review it and provide a quote soon.']);
    }
}