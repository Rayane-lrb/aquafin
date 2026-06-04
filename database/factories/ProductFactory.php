<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'stock' => $this->faker->randomDigit(),
            'is_active' => $this->faker->boolean(),
            'is_flood_tool' => $this->faker->boolean(),
            'product_category_id' => $this->faker->numberBetween(1, ProductCategory::count()),
        ];
    }
}
