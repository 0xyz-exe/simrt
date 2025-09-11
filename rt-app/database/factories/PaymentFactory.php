<?php

namespace Database\Factories;

use App\Models\Due;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'due_id' => Due::factory(),
            'amount' => fake()->numberBetween(25000, 100000),
            'method' => fake()->randomElement(['cash', 'transfer', 'qris']),
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
