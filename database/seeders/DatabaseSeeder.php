<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'tech user',
            'email' => 'tech@user.com',
            'role' => 'technieker',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'magazijn user',
            'email' => 'mag@user.com',
            'role' => 'magazijnBeheerder',
            'password' => bcrypt('password'),
        ]);

        // ProductCategory::factory(5)->create();
        // Product::factory(10)->create();
        // Suggestion::factory(10)->create();

    }
}
