<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log; // For debugging

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisteredShopController extends Controller
{
    /**
     * Handle an incoming shop registration request.
     */
    public function store(Request $request)
    {
        // 1. Validate all the form data
        $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'owner_phone' => ['required', 'string', 'max:20'],

            'shop_name' => ['required', 'string', 'max:255'],
            'shop_description' => ['required', 'string'],
            'shop_phone' => ['required', 'string', 'max:20'],

            'address' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:10'],
            'delivery_coverage' => ['required', 'string'],

            'permit_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        DB::beginTransaction();
        try {
            // 2. Handle the File Upload
            $permitPath = $request->file('permit_file')->store('permits', 'public');

            // 3. Create the User (the Shop Owner)
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
                'status' => 'Active', // User account is active, shop is pending
            ]);

            // 4. Create the Shop and link it to the new User
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;
            $shop->description = $request->shop_description;
            $shop->phone = $request->shop_phone;
            $shop->address = $request->address;
            $shop->zip_code = $request->zip_code;
            $shop->delivery_coverage = $request->delivery_coverage;
            $shop->permit_url = $permitPath;
            $shop->status = 'pending';
            $shop->save();

            DB::commit();

            // 5. Log the user in
            Auth::login($user);

            // 6. Redirect directly to vendor dashboard
            return redirect()->route('vendor.dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shop Registration Failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }
}