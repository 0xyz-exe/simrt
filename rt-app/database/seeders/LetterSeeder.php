<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\Resident;
use App\Models\User;

class LetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residents = Resident::all();
        $admin = User::where('is_admin', true)->first();

        if ($residents->count() === 0) {
            $this->command->error('No residents found. Run SampleDataSeeder first.');
            return;
        }

        // Fallback admin if not found
        if (!$admin) {
            $admin = User::first();
            if (!$admin) {
                $this->command->error('No users found. Create admin user first.');
                return;
            }
        }

        $letterTypes = ['domisili', 'pengantar', 'keterangan'];
        $purposes = [
            'domisili' => [
                'Untuk keperluan melamar kerja',
                'Untuk persyaratan pendaftaran sekolah',
                'Untuk membuka rekening bank',
                'Untuk perpanjangan SIM',
                'Untuk pendaftaran BPJS'
            ],
            'pengantar' => [
                'Untuk keperluan administrasi desa',
                'Untuk mengurus KTP yang hilang',
                'Untuk pendaftaran usaha',
                'Untuk keperluan nikah',
                'Untuk pengajuan bantuan sosial'
            ],
            'keterangan' => [
                'Untuk keperluan beasiswa',
                'Untuk keterangan tidak mampu',
                'Untuk keterangan usaha rumahan',
                'Untuk keterangan kehilangan dokumen',
                'Untuk keterangan belum menikah'
            ]
        ];

        $statuses = ['pending', 'approved', 'rejected'];
        $notes = [
            'Mohon dipercepat untuk keperluan mendesak',
            'Terima kasih',
            null,
            'Dibutuhkan segera',
            'Untuk keperluan hari Senin'
        ];

        // Create 15 sample letters
        for ($i = 0; $i < 15; $i++) {
            $resident = $residents->random();
            $type = $letterTypes[array_rand($letterTypes)];
            $status = $statuses[array_rand($statuses)];

            // Bias toward pending for demo
            if ($i < 5) {
                $status = 'pending';
            }

            $letter = Letter::create([
                'resident_id' => $resident->id,
                'type' => $type,
                'purpose' => $purposes[$type][array_rand($purposes[$type])],
                'notes' => $notes[array_rand($notes)],
                'status' => $status,
                'created_at' => now()->subDays(rand(1, 30))
            ]);

            // Set approval data for non-pending letters
            if ($status !== 'pending') {
                $letter->update([
                    'approved_by' => $admin->id,
                    'approved_at' => $letter->created_at->addDays(rand(1, 3))
                ]);
            }
        }

        $this->command->info('Created 15 sample letters');
    }
}
