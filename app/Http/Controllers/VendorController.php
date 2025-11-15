<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class VendorController extends Controller
    {
        public function dashboard()
        {
            // This returns your new vendor/dashboard.blade.php
            return view('vendor.dashboard'); 
        }
    }