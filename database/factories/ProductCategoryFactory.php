<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $icons = [
            'fas-box',
            'fas-wrench',
            'fas-cog',
            'fas-tools',
            'fas-screwdriver',
            'fas-hammer',
            'fas-gear'
        ];

        return [
            'icon' => fake()->randomElement($icons),
            'title' => fake()->unique()->words(rand(1, 3), true),
            'description' => fake()->optional()->paragraph(),
            'is_visible' => true,
            'parent_id' => null,
            'order' => 0
        ];
    }

    /**
     * Define a state for child categories.
     */
    public function child(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => \App\Models\ProductCategory::factory(),
            ];
        });
    }
}
