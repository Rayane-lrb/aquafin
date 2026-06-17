<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuggestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'image' => null,
            'status' => $this->faker->randomElement(['in behandeling', 'goedgekeurd', 'afgekeurd']),
        ];
    }
}
