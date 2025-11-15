<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Show the public landing page.
     */
    public function show()
    {
        return view('landing'); // This returns your new landing.blade.php
    }
}