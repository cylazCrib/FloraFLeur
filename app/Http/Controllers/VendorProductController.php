<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required', 'description' => 'required', 'price' => 'required|numeric',
            'category' => 'required', 'occasion' => 'required', 'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('products', 'public');

        Auth::user()->shop->products()->create([
            'name' => $request->name, 'description' => $request->description,
            'price' => $request->price, 'category' => $request->category,
            'occasion' => $request->occasion, 'image' => $path
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) abort(403);

        $data = $request->validate([
            'name' => 'required', 'description' => 'required', 'price' => 'required|numeric',
            'category' => 'required', 'occasion' => 'required', 'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $oldImage = $product->getRawOriginal('image');
            if ($oldImage) Storage::disk('public')->delete($oldImage);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->back();
    }

    public function destroy(Product $product)
{
    $userShop = Auth::user()->shop;

    // Double check the IDs in your console if this fails
    if (!$userShop || $product->shop_id !== $userShop->id) {
        // Log the IDs to your laravel.log so you can see why it's failing
        \Log::error("Unauthorized Delete: Product Shop ID {$product->shop_id} vs User Shop ID {$userShop->id}");
        abort(403, 'This product does not belong to your shop.');
    }

    // Get raw path for deletion
    $oldImage = $product->getRawOriginal('image');
    if ($oldImage) {
        Storage::disk('public')->delete($oldImage);
    }

    $product->delete();

    return redirect()->back();
}
}