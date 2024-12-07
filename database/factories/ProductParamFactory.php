<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductParam>
 */
class ProductParamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paramTypes = [
            'text',
            'number',
            'select',
            'multiselect',
            'boolean'
        ];

        return [
            'name' => fake()->words(rand(1, 2), true),
            'type' => fake()->randomElement($paramTypes),
            'allow_filtering' => fake()->boolean(70) // 70% chance of being filterable
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function ($param) {
            // Additional configuration if needed
        })->afterCreating(function ($param) {
            // Additional actions after creation if needed
        });
    }
}
