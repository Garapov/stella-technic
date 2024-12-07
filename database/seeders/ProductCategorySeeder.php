<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main parent categories
        $parentCategories = ProductCategory::factory()
            ->count(5)
            ->create();

        // For each parent category, create 2-4 child categories
        foreach ($parentCategories as $parentCategory) {
            ProductCategory::factory()
                ->count(rand(2, 4))
                ->create([
                    'parent_id' => $parentCategory->id
                ]);
        }

        // For some child categories, create grandchild categories
        $childCategories = ProductCategory::whereNotNull('parent_id')->get();
        $selectedChildren = $childCategories->random(min(5, $childCategories->count()));

        foreach ($selectedChildren as $childCategory) {
            ProductCategory::factory()
                ->count(rand(1, 3))
                ->create([
                    'parent_id' => $childCategory->id
                ]);
        }
    }
}
