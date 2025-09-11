<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@rt.local')->first();

        $announcements = [
            [
                'title' => 'Gotong Royong Minggu Depan',
                'content' => 'Dihimbau kepada seluruh warga RT 05/RW 02 untuk mengikuti kegiatan gotong royong yang akan dilaksanakan pada hari Minggu, 27 Agustus 2025 pukul 07.00 WIB. Kegiatan meliputi pembersihan lingkungan dan perbaikan jalan.',
                'published_at' => now()->subDays(2),
                'created_by' => $adminUser->id
            ],
            [
                'title' => 'Pembayaran Iuran Bulanan Agustus 2025',
                'content' => 'Iuran bulanan untuk bulan Agustus 2025 sebesar Rp 50.000 sudah dapat dibayarkan kepada bendahara RT. Pembayaran dapat dilakukan setiap hari Senin-Jumat pukul 19.00-21.00 WIB di rumah Pak RT.',
                'published_at' => now()->subDays(5),
                'created_by' => $adminUser->id
            ],
            [
                'title' => 'Peraturan Baru Area Parkir',
                'content' => 'Mulai tanggal 1 September 2025, area parkir di jalan utama akan ditata ulang. Mohon warga untuk tidak parkir di area yang sudah diberi marka merah. Terima kasih atas kerjasamanya.',
                'published_at' => now()->subDays(1),
                'created_by' => $adminUser->id
            ]
        ];

        foreach ($announcements as $announcement) {
            \App\Models\Announcement::create($announcement);
        }
    }
}
