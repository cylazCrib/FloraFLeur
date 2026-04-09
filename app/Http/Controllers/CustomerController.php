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
    /**
     * Display the Customer Dashboard with all required data properly formatted.
     */
    public function dashboard()
    {
        // 1. Fetch products (Main Shop Grid & Favorites source)
        $products = Product::latest()->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'description' => $p->description,
            'price' => (float)$p->price, 
            'image' => $p->image ? Storage::url($p->image) : null,
            'category' => $p->category ?? 'bouquet',
            'occasion' => $p->occasion ?? 'all',
            'shop_id' => $p->shop_id,
            'shop_name' => $p->shop->name ?? 'Flora Fleur Partner'
        ]);
        
        // 2. Standard Orders (Purchase History + Receipt Data)
        // [FIX] Eager loading 'shop' through the order to show the florist name on receipt
        $orders = Order::with(['items', 'shop'])
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
                    'payment_method' => $order->payment_method,
                    'payment_reference' => $order->payment_reference,
                    // [NEW] Shop info for the receipt header
                    'shop_name' => $order->shop->name ?? 'Flora Fleur Partner',
                    'created_at' => $order->created_at->format('M d, Y h:i A'),
                    'items' => $order->items->map(fn($item) => [
                        'name' => $item->product_name,
                        'price' => (float)$item->price,
                        'quantity' => (int)$item->quantity,
                        'image' => $item->image ? Storage::url($item->image) : null,
                    ]),
                ];
            });

        // 3. Custom Requests (Mapped for history UI)
        $requests = CustomRequest::with('shop')->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function($req) {
                return [
                    'id' => $req->id,
                    'type' => 'request',
                    'order_number' => 'REQ-'.$req->id,
                    'status' => $req->status,
                    'description' => $req->description,
                    'total_amount' => (float)($req->vendor_quote ?? $req->budget ?? 0),
                    'budget' => (float)$req->budget,
                    'vendor_quote' => $req->vendor_quote ? (float)$req->vendor_quote : null,
                    'driver_name' => $req->driver_name ?? null,
                    'payment_method' => 'Custom Quote',
                    'shop_name' => $req->shop->name ?? 'Flora Fleur Partner',
                    'created_at' => $req->created_at->format('M d, Y h:i A'),
                    'occasion' => $req->occasion,
                    'items' => [], 
                ];
            });

        // 4. Fetch Shops with nested products
        $shops = Shop::where('status', 'approved')
            ->with('products')
            ->latest()
            ->get()
            ->map(function($shop) {
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'description' => $shop->description,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'email' => $shop->email,
                    'gcash_qr_url' => $shop->gcash_qr_url,
                    'maya_qr_url' => $shop->maya_qr_url,
                    'payment_instructions' => $shop->payment_instructions,
                    'products' => $shop->products->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => (float)$p->price,
                        'image' => $p->image ? Storage::url($p->image) : null,
                        'category' => $p->category,
                        'occasion' => $p->occasion
                    ])
                ];
            });

        $occasions = ['Birthday', 'Anniversary', 'Valentines', 'Mothers Day', 'Graduation', 'Funeral', 'Just Because'];

        return Inertia::render('Customer/Dashboard', [
            'products' => $products,
            'orders' => $orders,
            'requests' => $requests,
            'shops' => $shops,
            'occasions' => $occasions,
            'user' => Auth::user()
        ]);
    }

    /**
     * Handle multi-shop checkout and order creation.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
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

            $cleanPrice = (float) str_replace([',', '₱', ' '], '', $product->price);
            $qty = (int) $item['qty'];

            $ordersByShop[$shopId]['items'][] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $cleanPrice,
                'qty' => $qty,
                'image' => $product->getRawOriginal('image') 
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
                    'payment_method' => $request->payment_method,
                    'payment_reference' => $request->payment_reference
                ]);

                foreach ($shopData['items'] as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'product_name' => $itemData['product_name'],
                        'quantity' => $itemData['qty'],
                        'price' => $itemData['price'],
                        'image' => $itemData['image'],
                    ]);
                }
            }
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error placing order.');
        }
    }

    /**
     * Submit a bespoke floral design request.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'description' => 'required|string',
            'contact_number' => 'required|string',
            'date_needed' => 'required',
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

        return redirect()->back();
    }

    /**
     * Update user account details.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->back();
    }
}