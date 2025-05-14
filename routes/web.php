<?php

use App\Http\Controllers\ProfileController;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::prefix("/")
    ->name("client.")
    ->group(function () {
        Route::view("/", "client.index")->name("index");
        Route::view("/constructor", "client.constructor")->name("constructor");
        Route::view("/constructor/embeded", "client.constructor_embeded")->name("constructor_embeded");
        Route::view("/catalog", "client.catalog.all")->name("catalog.all");
        Route::view("/catalog/brands", "client.brands.index")->name(
            "brands.index"
        );
        Route::view("/catalog/brands/{slug}", "client.brands.show")->name(
            "brands.show"
        );
        Route::view("/catalog/popular", "client.popular_products")->name(
            "catalog.popular"
        );
        Route::view("/catalog/{slug}", "client.catalog")->name("catalog");
        Route::view(
            "/catalog/products/{product_slug}",
            "client.product_detail"
        )->name("product_detail");

        Route::view("/cart", "client.cart")->name("cart");
        Route::view("/checkout", "client.checkout")->name("checkout");
        // Route::get('/simple/{page:slug}', function ($page) {
        //     logger()->info('Page route hit:', ['page' => $page]);
        //     return view('client.simple', ['page' => $page]);
        // })->name('simple');
        Route::view("/favorites", "client.favorites")->name("favorites");
        Route::get("/thanks", function () {
            $orderId = session("order_id", null);
            return view("client.thanks", ["orderId" => $orderId]);
        })->name("thanks");
        // Route::view('/checkout/thanks', 'client.thanks')->name('thanks');
        Route::view("/blog/{category_slug}/{slug}", "client.posts.show")->name(
            "posts.show"
        );
        Route::view("/blog", "client.posts.index")->name("posts.index");
        Route::view("/articles/{slug}", "client.articles.show")->name(
            "articles.show"
        );
        Route::view("/articles", "client.articles.index")->name(
            "articles.index"
        );
        Route::view("/search", "client.search")->name("search");
        Route::view("/certificates", "client.certificates")->name(
            "certificates"
        );
        Route::view("/vacancies", "client.vacancies")->name("vacancies");
        Route::view("/workers", "client.workers")->name("workers");
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

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit"
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update"
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy"
    );

    Route::get(
        "/admin/import-products/{import}/failed-rows/download",
        DownloadImportFailureCsv::class
    )->name("filament.imports.failed-rows.download");
});

Route::middleware("auth")->group(function () {
    Route::view("/profile/orders", "orders.index")->name("orders.index");
});

require __DIR__ . "/auth.php";
