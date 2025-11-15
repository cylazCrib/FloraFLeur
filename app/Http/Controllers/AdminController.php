<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class AdminController extends Controller
    {
        public function dashboard()
        {
            // This returns your new admin/dashboard.blade.php
            return view('admin.dashboard'); 
        }
    }