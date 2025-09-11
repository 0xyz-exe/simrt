<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat beberapa keluarga contoh
        $households = [
            [
                'nomor_kk' => '3201012345678901',
                'alamat' => 'Jl. Mawar No. 12, RT 05/RW 02',
                'rt' => '05',
                'rw' => '02'
            ],
            [
                'nomor_kk' => '3201012345678902',
                'alamat' => 'Jl. Melati No. 8, RT 05/RW 02',
                'rt' => '05',
                'rw' => '02'
            ],
            [
                'nomor_kk' => '3201012345678903',
                'alamat' => 'Jl. Anggrek No. 15, RT 05/RW 02',
                'rt' => '05',
                'rw' => '02'
            ]
        ];

        foreach ($households as $householdData) {
            $household = \App\Models\Household::create($householdData);

            // Buat anggota keluarga untuk setiap household
            if ($household->nomor_kk === '3201012345678901') {
                // Keluarga Budi Santoso (untuk user warga1@rt.local)
                $kepala = \App\Models\Resident::create([
                    'nik' => '3201010101850001',
                    'nama' => 'Budi Santoso',
                    'phone' => '08123456789',
                    'email' => 'warga1@rt.local',
                    'household_id' => $household->id,
                    'is_head' => true
                ]);

                $household->update(['kepala_keluarga_id' => $kepala->id]);

                \App\Models\Resident::create([
                    'nik' => '3201010202870001',
                    'nama' => 'Siti Rahayu',
                    'phone' => '08123456790',
                    'email' => null,
                    'household_id' => $household->id,
                    'is_head' => false
                ]);

                \App\Models\Resident::create([
                    'nik' => '3201010303050001',
                    'nama' => 'Ahmad Budi',
                    'phone' => null,
                    'email' => null,
                    'household_id' => $household->id,
                    'is_head' => false
                ]);
            } elseif ($household->nomor_kk === '3201012345678902') {
                // Keluarga Andi
                $kepala = \App\Models\Resident::create([
                    'nik' => '3201010404800001',
                    'nama' => 'Andi Wijaya',
                    'phone' => '08123456791',
                    'email' => 'andi@example.com',
                    'household_id' => $household->id,
                    'is_head' => true
                ]);

                $household->update(['kepala_keluarga_id' => $kepala->id]);

                \App\Models\Resident::create([
                    'nik' => '3201010505820001',
                    'nama' => 'Maya Sari',
                    'phone' => '08123456792',
                    'email' => null,
                    'household_id' => $household->id,
                    'is_head' => false
                ]);
            } else {
                // Keluarga Candra (hanya kepala keluarga)
                $kepala = \App\Models\Resident::create([
                    'nik' => '3201010606750001',
                    'nama' => 'Candra Kusuma',
                    'phone' => '08123456793',
                    'email' => 'candra@example.com',
                    'household_id' => $household->id,
                    'is_head' => true
                ]);

                $household->update(['kepala_keluarga_id' => $kepala->id]);
            }
        }
    }
}
