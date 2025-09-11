<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific activities
        Activity::create([
            'title' => 'Kerja Bakti Membersihkan Selokan',
            'description' => 'Kegiatan gotong royong membersihkan selokan di sekitar wilayah RT untuk mencegah banjir saat musim hujan. Semua warga diharapkan dapat berpartisipasi membawa alat kebersihan masing-masing.',
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '10:00',
            'location' => 'Jalan Utama RT',
            'type' => 'gotong_royong',
            'status' => 'scheduled',
            'max_participants' => 50,
            'current_participants' => 15,
            'requirements' => 'Membawa sapu, cangkul, dan sarung tangan',
            'contact_person' => 'Pak RT',
            'contact_phone' => '081234567890',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Activity::create([
            'title' => 'Rapat Koordinasi Bulanan RT',
            'description' => 'Rapat rutin bulanan untuk membahas program kerja RT, laporan keuangan, dan isu-isu terkini yang perlu dibahas bersama seluruh pengurus dan perwakilan warga.',
            'start_date' => now()->addDays(14)->format('Y-m-d'),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'location' => 'Balai RT',
            'type' => 'rapat',
            'status' => 'scheduled',
            'max_participants' => 30,
            'current_participants' => 8,
            'requirements' => 'Membawa alat tulis',
            'contact_person' => 'Sekretaris RT',
            'contact_phone' => '081234567891',
            'is_active' => true,
            'is_featured' => false,
        ]);

        Activity::create([
            'title' => 'Senam Pagi Bersama',
            'description' => 'Kegiatan senam pagi rutin setiap minggu untuk menjaga kesehatan warga RT. Dipandu oleh instruktur senam berpengalaman dan terbuka untuk semua usia.',
            'start_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '06:00',
            'end_time' => '07:00',
            'location' => 'Lapangan RT',
            'type' => 'olahraga',
            'status' => 'scheduled',
            'max_participants' => null,
            'current_participants' => 25,
            'requirements' => 'Membawa matras dan air minum',
            'contact_person' => 'Ibu Sari',
            'contact_phone' => '081234567892',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Activity::create([
            'title' => 'Lomba 17 Agustus',
            'description' => 'Rangkaian lomba memperingati HUT RI ke-78 dengan berbagai kategori: lomba balap karung, panjat pinang, makan kerupuk, dan doorprize menarik.',
            'start_date' => now()->addDays(21)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'location' => 'Lapangan RT',
            'type' => 'acara_khusus',
            'status' => 'scheduled',
            'max_participants' => 100,
            'current_participants' => 35,
            'requirements' => 'Pendaftaran di Pak RT',
            'contact_person' => 'Panitia 17 Agustus',
            'contact_phone' => '081234567893',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Activity::create([
            'title' => 'Pengajian Rutin Ibu-Ibu',
            'description' => 'Pengajian rutin mingguan untuk ibu-ibu dengan kajian Islam dan arisan sosial. Kegiatan ini juga menjadi ajang silaturahmi antar warga.',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'location' => 'Masjid Al-Ikhlas',
            'type' => 'keagamaan',
            'status' => 'scheduled',
            'max_participants' => null,
            'current_participants' => 20,
            'requirements' => 'Membawa Al-Quran dan sajadah',
            'contact_person' => 'Ibu Ketua RT',
            'contact_phone' => '081234567894',
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Create additional random activities
        Activity::factory()->count(10)->create();
    }
}
