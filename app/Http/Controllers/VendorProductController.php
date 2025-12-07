<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VendorProductController extends Controller
{
    /**
     * READ: Show the products page.
     */
    public function index()
    {
        // Get the logged-in vendor's shop
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('vendor.dashboard')->with('error', 'Shop not found. Please contact support.');
        }

        // Get products for this shop, newest first
        $products = $shop->products()->latest()->get();

        return view('vendor.products', compact('products'));
    }

    /**
     * CREATE: Save a new product.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'category' => 'required|string|in:bouquet,box,standee,potted', // <--- NEW RULE
        'image' => 'required|image|max:2048',
    ]);

    try {
        $shop = Auth::user()->shop;
        $path = $request->file('image')->store('products', 'public');

        $shop->products()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category, // <--- SAVE CATEGORY
            'image' => $path,
        ]);

        return response()->json(['message' => 'Product added successfully!']);

    } catch (\Exception $e) {
        Log::error($e);
        return response()->json(['message' => 'Error saving product.'], 500);
    }
}

    /**
     * UPDATE: Save changes to an existing product.
     */
    public function update(Request $request, Product $product)
{
    if ($product->shop_id !== Auth::user()->shop->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'category' => 'required|string|in:bouquet,box,standee,potted', // <--- NEW RULE
        'image' => 'nullable|image|max:2048',
    ]);

    try {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category, // <--- UPDATE CATEGORY
        ];

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json(['message' => 'Product updated successfully!']);

    } catch (\Exception $e) {
        Log::error($e);
        return response()->json(['message' => 'Error updating product.'], 500);
    }
}
    /**
     * DELETE: Remove a product.
     */
    public function destroy(Product $product)
    {
        // Security Check
        if ($product->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete image file
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!']);
    }
}