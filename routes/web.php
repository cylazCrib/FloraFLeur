<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;


Route::get('/', [LandingPageController::class, 'show'])->name('landing');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');