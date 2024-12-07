<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random Image IDs from the database
        $imageIds = \App\Models\Image::inRandomOrder()->take(6)->pluck('id')->toArray();
        

        $price = $this->faker->numberBetween(100, 10000);
        $discount = $this->faker->numberBetween(5, 30);
        $new_price = round($price * (1 - $discount / 100), 2);

        // Get random Category IDs
        $categoryIds = \App\Models\ProductCategory::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();

        // Get random Product Param Item IDs
        $paramItemIds = \App\Models\ProductParamItem::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();

        return [
            'name' => $this->faker->word,
            'image' => $imageIds[0],
            'price' => $price,
            'new_price' => $new_price,
            'count' => $this->faker->numberBetween(0, 100),
            'gallery' => array_slice($imageIds, 1, rand(1, 5)),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (\App\Models\Product $product) {
            // Sync categories
            // $categoryIds = \App\Models\ProductCategory::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $categoryIds = [1, 2, 3];
            $product->categories()->sync($categoryIds);
            
            // Sync product params
            $paramItemIds = \App\Models\ProductParamItem::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $product->paramItems()->sync($paramItemIds);
            

            $paramItems = $product->paramItems;
        
            // Do something with paramItems
            // dd($paramItems);
            
            foreach ($paramItems as $paramItem) {
                $imageIds = \App\Models\Image::inRandomOrder()->take(6)->pluck('id')->toArray();
                
                ProductVariant::create([
                    'product_id' => $product->id,
                    'product_param_item_id' => $paramItem->id,
                    'name' => $product->name . ' ' . $paramItem->title,
                    'price' => $product->price,
                    'new_price' => $product->new_price,
                    'image' => $imageIds[0]
                ]);
            }

            $product->save();
        });
    }
}
