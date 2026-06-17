<?php

namespace Database\Seeders;

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
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'role' => 'admin', 'password' => bcrypt('password')],
        );

        User::firstOrCreate(
            ['email' => 'tech@user.com'],
            ['name' => 'tech user', 'role' => 'technieker', 'password' => bcrypt('password')],
        );

        User::firstOrCreate(
            ['email' => 'mag@user.com'],
            ['name' => 'magazijn user', 'role' => 'magazijnBeheerder', 'password' => bcrypt('password')],
        );
        Suggestion::factory(10)->create();

    }
}
