<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Due>
 */
class DueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2023, 2024),
            'amount' => fake()->randomElement([25000, 50000, 75000, 100000]),
            'status' => fake()->randomElement(['unpaid', 'paid']),
        ];
    }
}
