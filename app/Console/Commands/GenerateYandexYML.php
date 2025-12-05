<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Illuminate\Console\Command;

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

class GenerateYandexYML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:yandex-yml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Yandex YML';

    public $categories;
    public $offers;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->categories = ProductCategory::where('is_visible', true)->get();
        $this->offers = ProductVariant::all();

        $file = public_path('yandex_market.xml');

        $settings = (new Settings())
            ->setOutputFile($file)
            ->setEncoding('UTF-8')
        ;

        // Creating ShopInfo object (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#shop)
        $shopInfo = (new ShopInfo())
            ->setName(env('APP_NAME'))
            ->setCompany(env('APP_NAME'))
            ->setUrl(env('APP_URL'))
        ;

        // Creating currencies array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#currencies)
        $currencies = [];
        $currencies[] = (new Currency())
            ->setId('RUR')
            ->setRate(1)
        ;

        // Creating categories array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#categories)
        $categories = [];

        $this->categories->each(function ($category) use (&$categories) {
            $categories[] = (new Category())
                ->setId($category->id)
                ->setName($category->title)
                ->setParentId($category->parent ? $category->parent->id : null)
            ;
        });

        // Creating offers array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#offers)
        $offers = [];
        $this->offers->each(function ($offer) use (&$offers) {
            $offers[] = (new OfferSimple())
                ->setId($offer->id)
                ->setAvailable($offer->is_visible)
                ->setUrl(env('APP_URL') . '/' . $offer->urlChain())
                ->setPrice($offer->price)
                ->setCurrencyId('RUR')
                ->setCategoryId($offer->product && $offer->product->category ? $offer->product->category->id : null)
                ->setDelivery(false)
                ->setName($offer->name ?? $offer->h1)
            ;
        });

        // Optional creating deliveries array (https://yandex.ru/support/partnermarket/elements/delivery-options.xml)
        $deliveries = [];
        // $deliveries[] = (new Delivery())
        //     ->setCost(2)
        //     ->setDays(1)
        //     ->setOrderBefore(14)
        // ;

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers,
            $deliveries
        );
    }
}
