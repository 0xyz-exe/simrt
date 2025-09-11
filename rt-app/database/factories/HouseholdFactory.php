<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Household>
 */
class HouseholdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_kk' => fake()->unique()->numerify('###############'),
            'alamat' => fake()->streetAddress(),
            'rt' => fake()->numberBetween(1, 20),
            'rw' => fake()->numberBetween(1, 10),
            'kepala_keluarga_id' => null,
        ];
    }
}
