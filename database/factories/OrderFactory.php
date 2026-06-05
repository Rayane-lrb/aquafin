<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::where('role', 'technieker')->inRandomOrder()->first()?->id
                            ?? User::factory()->create(['role' => 'technieker'])->id,
            'product_id' => Product::inRandomOrder()->first()?->id
                            ?? Product::factory()->create()->id,
            'quantity'   => $this->faker->numberBetween(1, 50),
            'status'     => $this->faker->randomElement(['in behandeling', 'goedgekeurd', 'afgekeurd'])
        ];
    }
}
