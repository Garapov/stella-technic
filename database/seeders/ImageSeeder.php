<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Ensure storage is linked
        if (!file_exists(public_path("storage"))) {
            \Log::info("Creating storage symlink...");
            \Artisan::call("storage:link");
        }

        // Create temporary directory if it doesn't exist
        $tempDir = storage_path("app/temp");
        if (!file_exists($tempDir)) {
            \Log::info("Creating temp directory: {$tempDir}");
            mkdir($tempDir, 0755, true);
        }

        for ($i = 0; $i < 10; $i++) {
            try {
                \Log::info("Attempting to create image {$i}");

                // Create a new image with GD
                $width = 640;
                $height = 480;
                $image = imagecreatetruecolor($width, $height);

                // Fill with random color
                $color = imagecolorallocate(
                    $image,
                    rand(0, 255),
                    rand(0, 255),
                    rand(0, 255)
                );
                imagefill($image, 0, 0, $color);

                // Add some random shapes
                for ($j = 0; $j < 5; $j++) {
                    $shape_color = imagecolorallocate(
                        $image,
                        rand(0, 255),
                        rand(0, 255),
                        rand(0, 255)
                    );
                    imagefilledellipse(
                        $image,
                        rand(0, $width),
                        rand(0, $height),
                        rand(20, 100),
                        rand(20, 100),
                        $shape_color
                    );
                }

                // Save to temporary file
                $tempImagePath = $tempDir . "/" . uniqid() . ".png";
                imagepng($image, $tempImagePath);
                imagedestroy($image);

                \Log::info("Temporary image created at: {$tempImagePath}");

                if (file_exists($tempImagePath)) {
                    // Create an UploadedFile instance
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempImagePath,
                        basename($tempImagePath),
                        "image/png",
                        null,
                        true
                    );

                    // Upload using Outerweb Image Library
                    $attributes = [
                        "title" => json_encode($faker->words(3)),
                        "alt" => json_encode($faker->words(3)),
                    ];

                    \Log::info("Uploading image with attributes:", $attributes);

                    $image = \App\Models\Image::upload(
                        $uploadedFile,
                        config("filesystems.default"),
                        $attributes
                    );

                    \Log::info(
                        "Image created successfully with UUID: {$image->uuid}"
                    );

                    // Clean up
                    @unlink($tempImagePath);
                } else {
                    \Log::error(
                        "Failed to create temporary image at: {$tempImagePath}"
                    );
                }
            } catch (\Exception $e) {
                \Log::error("Failed to create image: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
            }
        }

        // Clean up temporary directory
        @rmdir($tempDir);

        // Verify images were created
        $count = \App\Models\Image::count();
        \Log::info("Total images in database after seeding: {$count}");
    }
}
