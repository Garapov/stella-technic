<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductParamSeeder;
use Database\Seeders\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@admin.ru')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@admin.ru',
                'password' => Hash::make('password')
            ]);
        }

        $this->call([
            ProductCategorySeeder::class,
            ImageSeeder::class,
            ProductParamSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
