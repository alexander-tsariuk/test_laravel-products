<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
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
            'name' => 'Товар '.fake()->numerify(),
            'price' => fake()->randomFloat(2, 1, 99999),
            'category_id' => Category::factory(),
            'in_stock' => fake()->boolean(),
            'rating' => fake()->randomFloat(1, 1, 5),
        ];
    }
}
