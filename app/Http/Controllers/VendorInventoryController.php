<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class VendorInventoryController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;
        if (!$shop) return redirect()->route('vendor.dashboard');

        // Get items separated by type
        $items = $shop->inventory()->where('type', 'item')->latest()->get();
        $flowers = $shop->inventory()->where('type', 'flower')->latest()->get();

        return view('vendor.inventory', compact('items', 'flowers'));
    }

   public function store(Request $request)
    {
        // 1. Validate and GET only the valid data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'type' => 'required|in:item,flower',
            'code' => 'nullable|string|max:50',
        ]);

        // 2. Create using ONLY the validated data (ignores item_id)
        Auth::user()->shop->inventory()->create($validated);

        return response()->json(['message' => 'Item added successfully!']);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        if ($inventory->shop_id !== Auth::user()->shop->id) abort(403);

        // 1. Validate and GET only the valid data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'code' => 'nullable|string|max:50',
        ]);

        // 2. Update using ONLY the validated data (ignores item_id)
        $inventory->update($validated);

        return response()->json(['message' => 'Item updated successfully!']);
    }
    public function destroy(InventoryItem $inventory)
    {
        if ($inventory->shop_id !== Auth::user()->shop->id) abort(403);

        $inventory->delete();

        return response()->json(['message' => 'Item deleted successfully!']);
    }
}