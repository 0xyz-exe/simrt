<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['gotong_royong', 'rapat', 'acara_khusus', 'olahraga', 'sosial', 'keagamaan', 'pendidikan', 'kesehatan'];
        $statuses = ['scheduled', 'ongoing', 'completed'];

        $startDate = $this->faker->dateTimeBetween('now', '+2 months');
        $endDate = $this->faker->optional(0.3)->dateTimeBetween($startDate, $startDate->format('Y-m-d').' +7 days');

        return [
            'title' => $this->faker->randomElement([
                'Kerja Bakti Membersihkan Lingkungan RT',
                'Rapat Koordinasi Bulanan',
                'Lomba 17 Agustus',
                'Senam Pagi Bersama',
                'Pengajian Rutin',
                'Bakti Sosial untuk Anak Yatim',
                'Penyuluhan Kesehatan',
                'Gotong Royong Perbaikan Jalan'
            ]),
            'description' => $this->faker->paragraph(3),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'start_time' => $this->faker->optional(0.8)->time('H:i'),
            'end_time' => $this->faker->optional(0.6)->time('H:i'),
            'location' => $this->faker->randomElement([
                'Balai RT',
                'Lapangan RT',
                'Masjid Al-Ikhlas',
                'Jalan Utama RT',
                'Rumah Pak RT',
                'Taman RT'
            ]),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
            'max_participants' => $this->faker->optional(0.7)->numberBetween(10, 100),
            'current_participants' => $this->faker->numberBetween(0, 50),
            'requirements' => $this->faker->optional(0.5)->paragraph(1),
            'contact_person' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'is_active' => $this->faker->boolean(90),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
