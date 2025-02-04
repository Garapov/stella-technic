<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductParamSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        Artisan::call('permissions:sync');
        $role = Role::create(['name' => 'super_admin']);
        $role->givePermissionTo(Permission::where('guard_name', 'web')->get());

        if (!User::where('email', 'admin@admin.ru')->exists()) {
            $user = User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@admin.ru',
                'password' => Hash::make('password')
            ]);
            $user->assignRole('super_admin');
        }

        $this->call([
            ProductCategorySeeder::class,
            ImageSeeder::class,
            ProductParamSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
