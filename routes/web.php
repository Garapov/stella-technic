<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->name('client.')->group(function () {
    Route::view('/', 'client.index')->name('index');
    Route::view('/catalog', 'client.catalog')->name('catalog');
    Route::view('/catalog-alt', 'client.catalog_alt')->name('catalog-alt');
    Route::view('/product_detail', 'client.product_detail')->name('product_detail');
    Route::view('/cart', 'client.cart')->name('cart');
    Route::view('/checkout', 'client.checkout')->name('checkout');
    Route::view('/simple', 'client.simple')->name('simple');
    Route::view('/favorites', 'client.favorites')->name('favorites');
    Route::view('/checkout/thanks', 'client.thanks')->name('thanks');
});

Route::prefix('/dashboard')->name('dashboard.')->group(function () {
    Route::view('/', 'dashboard.index')->name('index');
})->middleware(['auth', 'verified']);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::view('/profile/orders', 'orders.index')->name('orders.index');
});

require __DIR__.'/auth.php';
