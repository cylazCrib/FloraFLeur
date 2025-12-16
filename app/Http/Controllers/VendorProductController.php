<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VendorProductController extends Controller
{
    // Define the list of occasions here so we can use it in both create and edit
    private $occasionsList = [
        'Birthday',
        'Anniversary',
        'Valentines',
        'Mothers Day',
        'Graduation',
        'Funeral',
        'Just Because'
    ];

    /**
     * READ: Show the products page.
     */
    public function index()
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('vendor.dashboard')->with('error', 'Shop not found.');
        }

        $products = $shop->products()->latest()->get();

        // Pass the list to the view
        $occasions = $this->occasionsList;

        return view('vendor.products', compact('products', 'occasions'));
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
            'category' => 'required|string|in:bouquet,box,standee,potted',
            'occasion' => 'required|string', // <--- VALIDATE OCCASION
            'image' => 'required|image|max:2048',
        ]);

        try {
            $shop = Auth::user()->shop;
            
            // Upload Image
            $path = $request->file('image')->store('products', 'public');

            $shop->products()->create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'occasion' => $request->occasion, // <--- SAVING IT NOW!
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
            'category' => 'required|string|in:bouquet,box,standee,potted',
            'occasion' => 'required|string', // <--- VALIDATE OCCASION
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'occasion' => $request->occasion, // <--- UPDATING IT NOW!
            ];

            if ($request->hasFile('image')) {
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
     * DELETE: Remove a product.
     */
    public function destroy(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!']);
    }
}