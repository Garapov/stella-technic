<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->name('client.')->group(function () {
    Route::view('/', 'client.index')->name('index');
    Route::view('/catalog/{slug}', 'client.catalog')->name('catalog');
    Route::view('/product_detail', 'client.product_detail')->name('product_detail');
    Route::view('/cart', 'client.cart')->name('cart');
    Route::view('/checkout', 'client.checkout')->name('checkout');
    Route::view('/simple', 'client.simple')->name('simple');
    Route::view('/favorites', 'client.favorites')->name('favorites');
    Route::view('/checkout/thanks', 'client.thanks')->name('thanks');
    Route::view('/blog/{category_slug}/{slug}', 'client.posts.show')->name('posts.show');
});

// Route::middleware(['auth', 'verified'])->prefix('/dashboard')->name('dashboard.')->group(function () {
//     Route::view('/', 'dashboard.index')->name('index');

//     Route::prefix('/categories')->name('categories.')->group(function () {
//         Route::view('/', 'dashboard.categories.index')->name('index');
//         Route::view('/create', 'dashboard.categories.add')->name('add');
//         Route::view('/edit/{slug}', 'dashboard.categories.edit')->name('edit');
//     });

//     Route::prefix('/products')->name('products.')->group(function () {
//         Route::view('/', 'dashboard.products.index')->name('index');
//         Route::view('/create', 'dashboard.products.add')->name('add');
//         Route::view('/edit/{slug}', 'dashboard.products.edit')->name('edit');
//     });
    
// });

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
