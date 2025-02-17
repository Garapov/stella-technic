<?php

namespace App\Livewire\General;

use App\Models\Product;
use App\Models\ProductCategory;
use Z3d0X\FilamentFabricator\Models\Page;
use Livewire\Component;
use Illuminate\Support\Facades\Route;

class Breadcrumbs extends Component
{
    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $currentRoute = Route::current();
        
        // Добавляем главную страницу
        $breadcrumbs[] = [
            'title' => 'Главная',
            'url' => route('client.index'),
            'active' => false
        ];

        // Проверяем параметры маршрута для страниц Fabricator
        $parameters = $currentRoute->parameters();
        if (isset($parameters['filamentFabricatorPage'])) {
            $page = $parameters['filamentFabricatorPage'];
            
            if ($page instanceof \Z3d0X\FilamentFabricator\Models\Page) {
                $breadcrumbs[] = [
                    'title' => $page->title,
                    'url' => null,
                    'active' => true
                ];
            }
        }
        // Проверяем обычные страницы
        elseif (in_array($currentRoute->getName(), ['filament-fabricator.page', 'client.simple'])) {
            $slug = $currentRoute->parameter('slug');
            
            if (!$slug) {
                $uri = request()->getRequestUri();
                $slug = trim($uri, '/');
            }
            
            $page = Page::where('slug', $slug)->first();
            
            if ($page) {
                $breadcrumbs[] = [
                    'title' => $page->title,
                    'url' => null,
                    'active' => true
                ];
            }
        }
        // В зависимости от текущего маршрута добавляем нужные хлебные крошки
        elseif ($currentRoute->getName() === 'client.catalog') {
            $breadcrumbs[] = [
                'title' => 'Каталог',
                'url' => route('client.catalog', ['slug' => 'all']),
                'active' => false
            ];

            // Добавляем текущую категорию
            $slug = $currentRoute->parameter('slug');
            if ($slug && $slug !== 'all') {
                $category = ProductCategory::where('slug', $slug)->first();
                if ($category) {
                    // Добавляем родительские категории
                    $parents = collect([]);
                    $parent = $category->parent;
                    while ($parent) {
                        $parents->push($parent);
                        $parent = $parent->parent;
                    }

                    // Добавляем родительские категории в обратном порядке
                    foreach ($parents->reverse() as $parent) {
                        $breadcrumbs[] = [
                            'title' => $parent->title,
                            'url' => route('client.catalog', ['slug' => $parent->slug]),
                            'active' => false
                        ];
                    }

                    // Добавляем текущую категорию
                    $breadcrumbs[] = [
                        'title' => $category->title,
                        'url' => null,
                        'active' => true
                    ];
                }
            }
        } 
        elseif ($currentRoute->getName() === 'client.product_detail') {
            $breadcrumbs[] = [
                'title' => 'Каталог',
                'url' => route('client.catalog', ['slug' => 'all']),
                'active' => false
            ];

            // Получаем текущий продукт
            $productSlug = $currentRoute->parameter('product_slug');
            $product = Product::where('slug', $productSlug)->first();
            
            if ($product) {
                // Добавляем первую категорию продукта
                $category = $product->categories->first();
                if ($category) {
                    // Добавляем родительские категории
                    $parents = collect([]);
                    $parent = $category->parent;
                    while ($parent) {
                        $parents->push($parent);
                        $parent = $parent->parent;
                    }

                    // Добавляем родительские категории в обратном порядке
                    foreach ($parents->reverse() as $parent) {
                        $breadcrumbs[] = [
                            'title' => $parent->title,
                            'url' => route('client.catalog', ['slug' => $parent->slug]),
                            'active' => false
                        ];
                    }

                    // Добавляем категорию продукта
                    $breadcrumbs[] = [
                        'title' => $category->title,
                        'url' => route('client.catalog', ['slug' => $category->slug]),
                        'active' => false
                    ];
                }

                // Добавляем название продукта
                $breadcrumbs[] = [
                    'title' => $product->name,
                    'url' => null,
                    'active' => true
                ];
            }
        }
        elseif ($currentRoute->getName() === 'client.cart') {
            $breadcrumbs[] = [
                'title' => 'Корзина',
                'url' => null,
                'active' => true
            ];
        }
        elseif ($currentRoute->getName() === 'client.brands') {
            $breadcrumbs[] = [
                'title' => 'Бренды',
                'url' => null,
                'active' => true
            ];
        }
        elseif ($currentRoute->getName() === 'client.search') {
            $breadcrumbs[] = [
                'title' => 'Поиск',
                'url' => null,
                'active' => true
            ];
        }
        elseif ($currentRoute->getName() === 'client.favorites') {
            $breadcrumbs[] = [
                'title' => 'Избранное',
                'url' => null,
                'active' => true
            ];
        }
        elseif ($currentRoute->getName() === 'orders.index') {
            $breadcrumbs[] = [
                'title' => 'Заказы',
                'url' => null,
                'active' => true
            ];
        }
        elseif ($currentRoute->getName() === 'client.catalog.popular') {
            $breadcrumbs[] = [
                'title' => 'Каталог',
                'url' => route('client.catalog', ['slug' => 'all']),
                'active' => false
            ];
            $breadcrumbs[] = [
                'title' => 'Популярные',
                'url' => null,
                'active' => true
            ];
        }

        

        return $breadcrumbs;
    }

    public function render()
    {
        return view('livewire.general.breadcrumbs', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}
