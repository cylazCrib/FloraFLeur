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
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('vendor.dashboard')->with('error', 'Shop not found.');
        }

        // Fetch products, latest first
        $products = $shop->products()->latest()->get();

        return view('vendor.products', compact('products'));
    }

    /**
     * CREATE: Save a new product via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|max:2048', // Max 2MB
        ]);

        try {
            $shop = Auth::user()->shop;
            
            // Store image in 'storage/app/public/products'
            $path = $request->file('image')->store('products', 'public');

            $shop->products()->create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $path,
            ]);

            return response()->json(['message' => 'Product added successfully!']);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Error saving product.', 'errors' => [$e->getMessage()]], 500);
        }
    }

    /**
     * UPDATE: Update an existing product via AJAX.
     */
    public function update(Request $request, Product $product)
    {
        // Authorization: Ensure product belongs to vendor's shop
        if ($product->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ];

            // Handle Image Update
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
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
     * DELETE: Remove a product via AJAX.
     */
    public function destroy(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully!']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Error deleting product.'], 500);
        }
    }
}