<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use \App\Models\ProductCategory;
use \App\Models\ProductVariant;
// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route('client.index'));
});

Breadcrumbs::for('catalog', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Каталог', route('client.catalog.all'));
});

Breadcrumbs::for('favorites', function (BreadcrumbTrail $trail) {
    $trail->parent('catalog');
    $trail->push('Избранное', route('client.favorites'));
});

Breadcrumbs::for('category', function (BreadcrumbTrail $trail, ProductCategory $category) {
    $trail->parent('catalog');

    $current = $category;
    $parents = collect();

    while ($current->parent_id && $current->parent_id != '-1') {
        $parentCategory = ProductCategory::find($current->parent_id);

        if ($parentCategory) {
            $parents->prepend($parentCategory);
            $current = $parentCategory;
        }        
    }

    foreach ($parents as $parentCategory) {
        $trail->push($parentCategory->title, route('client.catalog', $parentCategory->urlChain()));
    }

    $trail->push($category->title, route('client.catalog', $category->urlChain()));
});

Breadcrumbs::for('product', function (BreadcrumbTrail $trail, ProductVariant $variation) {
    $trail->parent('catalog');
    
    $all_categories = ProductCategory::all();

    $current = $variation->product->categories->last();

    // dd($variation);
    $parents = collect();

    while ($current->parent_id && $current->parent_id != '-1') {
        $parentCategory = $all_categories->find($current->parent_id);

        if ($parentCategory) {
            $parents->prepend($parentCategory);
            $current = $parentCategory;
        }        
    }

    foreach ($parents as $parentCategory) {
        $trail->push($parentCategory->title, route('client.catalog', $parentCategory->urlChain()));
    }

    $trail->push($variation->product->categories->last()->title, route('client.catalog', $variation->product->categories->last()->urlChain()));

    $trail->push($variation->sku, route('client.catalog', $variation->urlChain()));
});

Breadcrumbs::for('cart', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Корзина', route('client.cart'));
});

Breadcrumbs::for('checkout', function (BreadcrumbTrail $trail) {
    $trail->parent('cart');
    $trail->push('Оформление заказа', route('client.checkout'));
});


Breadcrumbs::for('page', function (BreadcrumbTrail $trail, $page) {
    $trail->parent('home');

    // Собираем родительские страницы
    $parents = collect();
    $current = $page->parent;

    while ($current) {
        $parents->prepend($current);
        $current = $current->parent;
    }

    foreach ($parents as $parentPage) {
        $trail->push($parentPage->title, route('filament-fabricator.page.show', $parentPage->slug));
    }

    $trail->push($page->title, route('filament-fabricator.page.show', $page->slug));
});