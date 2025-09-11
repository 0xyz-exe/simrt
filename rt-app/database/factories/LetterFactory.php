<?php

namespace Database\Factories;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resident_id' => Resident::factory(),
            'type' => fake()->randomElement(['domisili', 'pengantar', 'keterangan']),
            'purpose' => fake()->sentence(),
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'number' => null,
            'pdf_path' => null,
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}
