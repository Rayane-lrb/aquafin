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
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'image' => null,
            'status' => fake()->randomElement(['in behandeling', 'goedgekeurd', 'afgekeurd']),
        ];
    }
}
