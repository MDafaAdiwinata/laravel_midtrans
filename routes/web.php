<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Proses Checkout
Route::post('/checkout', [ShopController::class, 'checkout']);

// Proses Pembayarab + Notif
Route::post('/payment/callback', [ShopController::class, 'callback']);
