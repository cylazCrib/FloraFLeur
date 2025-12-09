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
        $products = Product::latest()->get();
        $orders = Order::with('items')->where('user_id', Auth::id())->latest()->get();
        // [FIX] Fetch Requests
        $requests = CustomRequest::where('user_id', Auth::id())->latest()->get();

        return view('customer.dashboard', compact('products', 'orders', 'requests'));
    }

    // [FIX] Profile Update Method
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
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
        
        DB::beginTransaction();
        try {
            // Create Order
            $order = Order::create([
                'shop_id' => 1, // Default or logic to find shop
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'customer_name' => $user->name,
                'customer_phone' => $user->phone ?? 'N/A',
                'customer_email' => $user->email,
                'delivery_address' => $user->address ?? 'Default',
                'delivery_date' => now()->addDays(3),
                'total_amount' => 0,
                'status' => 'Pending',
                'payment_method' => $request->payment_method
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $p = Product::find($item['id']);
                if ($p) {
                    $price = (float) $p->price;
                    $qty = (int) $item['qty'];
                    $total += $price * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'quantity' => $qty,
                        'price' => $price,
                    ]);
                }
            }
            $order->update(['total_amount' => $total]);
            
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