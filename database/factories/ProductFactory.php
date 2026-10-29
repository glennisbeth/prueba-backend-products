<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 10, 5000),
            'currency_id' => Currency::factory(),
            'tax_cost' => fake()->randomFloat(2, 1, 500),
            'manufacturing_cost' => fake()->randomFloat(2, 5, 2000),
        ];
    }
}