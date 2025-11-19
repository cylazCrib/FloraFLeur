<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log; // For debugging

class RegisteredShopController extends Controller
{
    /**
     * Handle an incoming shop registration request.
     */
    public function store(Request $request)
    {
        // 1. Validate all the form data
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all(),
            ], 422); // 422 is 'Unprocessable Entity'
        }

        try {
            // 2. Handle the File Upload
            // This stores the file in 'storage/app/public/permits'
            $permitPath = $request->file('permit_file')->store('permits', 'public');

            // 3. Create the User (the Shop Owner)
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'vendor', // Set their role to 'vendor'
            ]);

            // 4. Create the Shop and link it to the new User
            $shop = new Shop;
            $shop->user_id = $user->id; // Link to the user
            $shop->name = $request->shop_name;
            $shop->description = $request->shop_description;
            $shop->phone = $request->shop_phone;
            $shop->address = $request->address;
            $shop->zip_code = $request->zip_code;
            $shop->delivery_coverage = $request->delivery_coverage;
            $shop->permit_url = $permitPath; // Save the path to the file
            $shop->status = 'pending'; // Set status for admin approval
            $shop->save();

            // 5. Send a success response back to the JavaScript
            return response()->json([
                'success' => true,
                'message' => 'Your shop registration has been submitted for review!',
            ]);

        } catch (\Exception $e) {
            // Log any errors
            Log::error('Shop Registration Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['An unexpected error occurred. Please try again.'],
            ], 500);
        }
    }
}