<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Outerweb\ImageLibrary\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'disk' => 'local',
            'mime_type' => 'image/jpeg', // or use a random mime type
            'file_extension' => 'jpg', // or use a random file extension
            'width' => $this->faker->numberBetween(100, 2000),
            'height' => $this->faker->numberBetween(100, 2000),
            'size' => $this->faker->numberBetween(1024, 1048576), // Size in bytes
            'title' => json_encode($this->faker->words(3)), // Store as JSON
            'alt' => json_encode($this->faker->words(3)), // Store as JSON
        ];
    }
}
