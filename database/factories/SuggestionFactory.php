<?php

namespace Database\Factories;

use App\Models\Suggestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Suggestion>
 */
class SuggestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'product_id' => Product::inRandomOrder()->first()?->id ?? 1,
            'reason'     => $this->faker->sentence(),
            'is_active'  => true,
        ];
    }
}
