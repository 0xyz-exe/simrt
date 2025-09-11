<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $households = \App\Models\Household::all();

        // Create dues for last 3 months
        $months = [
            ['month' => 6, 'year' => 2025, 'amount' => 50000],  // Juni 2025
            ['month' => 7, 'year' => 2025, 'amount' => 50000],  // Juli 2025
            ['month' => 8, 'year' => 2025, 'amount' => 50000],  // Agustus 2025
        ];

        foreach ($months as $period) {
            foreach ($households as $household) {
                $due = \App\Models\Due::create([
                    'household_id' => $household->id,
                    'month' => $period['month'],
                    'year' => $period['year'],
                    'amount' => $period['amount'],
                    'status' => 'unpaid'
                ]);

                // Randomly mark some as paid (70% chance)
                if (rand(1, 10) <= 7) {
                    \App\Models\Payment::create([
                        'due_id' => $due->id,
                        'amount' => $period['amount'],
                        'method' => collect(['cash', 'transfer', 'qris'])->random(),
                        'paid_at' => now()->subDays(rand(1, 30)),
                        'notes' => null
                    ]);

                    $due->update(['status' => 'paid']);
                }
            }
        }
    }
}
