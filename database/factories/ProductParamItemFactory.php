<?php

namespace Database\Factories;

use App\Models\ProductParam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductParamItem>
 */
class ProductParamItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_param_id' => ProductParam::factory(),
            'title' => fake()->words(rand(1, 3), true),
            'value' => function (array $attributes) {
                $param = ProductParam::find($attributes['product_param_id']);
                if (!$param) {
                    return fake()->word();
                }

                return match ($param->type) {
                    'number' => fake()->numberBetween(1, 1000),
                    'boolean' => fake()->boolean(),
                    'text', 'select', 'multiselect' => fake()->words(rand(1, 3), true),
                    default => fake()->word(),
                };
            },
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function ($paramItem) {
            // Additional configuration if needed
        })->afterCreating(function ($paramItem) {
            // Additional actions after creation if needed
        });
    }
}
