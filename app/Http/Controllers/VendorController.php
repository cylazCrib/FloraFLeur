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
use Inertia\Inertia;

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) return Inertia::render('Dashboard', ['error' => 'No shop found for this user.']);

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
            ->where('shop_id', $shop->id)
            ->latest()
            ->get();

        return Inertia::render('Dashboard', compact(
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

    public function savePaymentQR(Request $request) {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        // Validate request
        $validated = $request->validate([
            'email' => 'required|email',
            'payment_instructions' => 'nullable|string',
            'gcash_qr' => 'nullable|image|mimes:jpeg,png|max:2048',
            'maya_qr' => 'nullable|image|mimes:jpeg,png|max:2048',
        ], [
            'gcash_qr.image' => 'GCash QR must be an image file',
            'gcash_qr.mimes' => 'GCash QR must be JPG or PNG format',
            'gcash_qr.max' => 'GCash QR must not exceed 2MB',
            'maya_qr.image' => 'Maya QR must be an image file',
            'maya_qr.mimes' => 'Maya QR must be JPG or PNG format',
            'maya_qr.max' => 'Maya QR must not exceed 2MB',
        ]);

        try {
            $updateData = ['email' => $validated['email']];

            // If payment instructions provided, store as JSON array
            if ($validated['payment_instructions']) {
                $updateData['payment_instructions'] = array_filter(
                    array_map('trim', explode("\n", $validated['payment_instructions']))
                );
            }

            // Handle GCash QR upload
            if ($request->hasFile('gcash_qr')) {
                $file = $request->file('gcash_qr');
                $filename = 'gcash_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("qr-codes/{$shop->id}", $filename, 'public');
                $updateData['gcash_qr_url'] = '/storage/' . $path;
            }

            // Handle Maya QR upload
            if ($request->hasFile('maya_qr')) {
                $file = $request->file('maya_qr');
                $filename = 'maya_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("qr-codes/{$shop->id}", $filename, 'public');
                $updateData['maya_qr_url'] = '/storage/' . $path;
            }

            // Update shop with payment data
            $shop->update($updateData);

            return response()->json([
                'message' => 'Payment settings updated successfully',
                'gcash_qr_url' => $shop->gcash_qr_url,
                'maya_qr_url' => $shop->maya_qr_url,
                'email' => $shop->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save payment settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitRequestQuote(Request $request, $id) {
        $user = Auth::user();
        $shop = $user->shop;
        
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        $customRequest = CustomRequest::find($id);
        
        if (!$customRequest || $customRequest->shop_id != $shop->id) {
            return response()->json(['error' => 'Request not found'], 404);
        }

        // Validate quote price
        $validated = $request->validate([
            'vendor_quote' => 'required|numeric|min:0',
            'quote_notes' => 'nullable|string'
        ]);

        try {
            // Update request with vendor quote and change status to reviewing
            $customRequest->update([
                'vendor_quote' => $validated['vendor_quote'],
                'status' => 'reviewing'
            ]);

            return response()->json([
                'message' => 'Quote saved. Click "Approve & Send to Customer" to notify them of your quote.',
                'vendor_quote' => $customRequest->vendor_quote
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit quote: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveRequestQuote(Request $request, $id) {
        $user = Auth::user();
        $shop = $user->shop;
        
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        $customRequest = CustomRequest::find($id);
        
        if (!$customRequest || $customRequest->shop_id != $shop->id) {
            return response()->json(['error' => 'Request not found'], 404);
        }
        
        if (!$customRequest->vendor_quote) {
            return response()->json(['error' => 'No quote to approve'], 400);
        }

        try {
            // Change status to approved
            $customRequest->update([
                'status' => 'approved'
            ]);

            // Create an Order record for this custom arrangement
            $orderNumber = 'ORD-' . date('YmdHis') . '-' . $customRequest->id;
            $order = Order::create([
                'shop_id' => $shop->id,
                'user_id' => $customRequest->user_id,
                'custom_request_id' => $customRequest->id,
                'order_number' => $orderNumber,
                'customer_name' => $customRequest->user->name,
                'customer_phone' => $customRequest->contact_number,
                'customer_email' => $customRequest->user->email,
                'delivery_address' => $customRequest->user->address ?? 'Not specified',
                'delivery_date' => $customRequest->date_needed ?? now()->addDays(5),
                'total_amount' => $customRequest->vendor_quote,
                'status' => 'pending',
                'payment_method' => 'pending'
            ]);

            // Create an OrderItem for the custom arrangement
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'product_name' => 'Custom Arrangement - ' . ($customRequest->occasion ?? 'Custom'),
                'quantity' => 1,
                'price' => $customRequest->vendor_quote
            ]);

            return response()->json([
                'message' => 'Quote approved! Customer has been notified at ' . $customRequest->user->email,
                'order_id' => $order->id,
                'order_number' => $orderNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to approve quote: ' . $e->getMessage()
            ], 500);
        }
    }
}