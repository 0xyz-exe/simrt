<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General RT Information
            [
                'key' => 'rt_name',
                'value' => 'RT 02/RW 04 Kedurus - Kepatihan',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Nama RT',
                'description' => 'Nama lengkap RT',
                'sort_order' => 1,
                'is_public' => true
            ],
            [
                'key' => 'rt_address',
                'value' => 'Jl. Melati No. 123, Kelurahan Sukamaju, Jakarta Selatan',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Alamat RT',
                'description' => 'Alamat lengkap wilayah RT',
                'sort_order' => 2,
                'is_public' => true
            ],
            [
                'key' => 'rt_description',
                'value' => 'RT 02/RW 04 Kedurus - Kepatihan adalah komunitas yang solid dan peduli sesama. Kami berkomitmen untuk menciptakan lingkungan yang nyaman, aman, dan sejahtera bagi seluruh warga.',
                'type' => 'rich_text',
                'group' => 'general',
                'label' => 'Deskripsi RT',
                'description' => 'Deskripsi singkat tentang RT',
                'sort_order' => 3,
                'is_public' => true
            ],

            // RT Officials
            [
                'key' => 'ketua_rt_name',
                'value' => 'Bapak Ahmad Suryadi',
                'type' => 'text',
                'group' => 'officials',
                'label' => 'Nama Ketua RT',
                'description' => 'Nama lengkap Ketua RT',
                'sort_order' => 1,
                'is_public' => true
            ],
            [
                'key' => 'sekretaris_name',
                'value' => 'Ibu Siti Rahayu',
                'type' => 'text',
                'group' => 'officials',
                'label' => 'Nama Sekretaris',
                'description' => 'Nama lengkap Sekretaris RT',
                'sort_order' => 2,
                'is_public' => true
            ],
            [
                'key' => 'bendahara_name',
                'value' => 'Bapak Dedi Kurniawan',
                'type' => 'text',
                'group' => 'officials',
                'label' => 'Nama Bendahara',
                'description' => 'Nama lengkap Bendahara RT',
                'sort_order' => 3,
                'is_public' => true
            ],

            // Contact Information
            [
                'key' => 'contact_phone',
                'value' => '(021) 1234-5678',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon RT',
                'sort_order' => 1,
                'is_public' => true
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '081234567890',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'WhatsApp',
                'description' => 'Nomor WhatsApp RT',
                'sort_order' => 2,
                'is_public' => true
            ],
            [
                'key' => 'contact_email',
                'value' => 'rt02rw04@kedurus.id',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Email',
                'description' => 'Email resmi RT',
                'sort_order' => 3,
                'is_public' => true
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/rt02rw04kedurus',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Facebook',
                'description' => 'Link Facebook RT',
                'sort_order' => 1,
                'is_public' => true
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/rt02rw04kedurus',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Instagram',
                'description' => 'Link Instagram RT',
                'sort_order' => 2,
                'is_public' => true
            ],

            // Landing Page Settings
            [
                'key' => 'hero_title',
                'value' => 'Selamat Datang di RT 02/RW 04 Kedurus - Kepatihan',
                'type' => 'text',
                'group' => 'landing',
                'label' => 'Judul Hero',
                'description' => 'Judul utama di halaman depan',
                'sort_order' => 1,
                'is_public' => true
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Sistem informasi digital untuk transparansi dan pelayanan warga yang lebih baik',
                'type' => 'text',
                'group' => 'landing',
                'label' => 'Subjudul Hero',
                'description' => 'Subjudul di halaman depan',
                'sort_order' => 2,
                'is_public' => true
            ],
            [
                'key' => 'show_announcements',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'landing',
                'label' => 'Tampilkan Pengumuman',
                'description' => 'Tampilkan section pengumuman di halaman depan',
                'sort_order' => 3,
                'is_public' => true
            ],
            [
                'key' => 'show_activities',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'landing',
                'label' => 'Tampilkan Kegiatan',
                'description' => 'Tampilkan section kegiatan di halaman depan',
                'sort_order' => 4,
                'is_public' => true
            ],
            [
                'key' => 'show_businesses',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'landing',
                'label' => 'Tampilkan UMKM',
                'description' => 'Tampilkan section UMKM di halaman depan',
                'sort_order' => 5,
                'is_public' => true
            ],
            [
                'key' => 'show_transparency',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'landing',
                'label' => 'Tampilkan Transparansi',
                'description' => 'Tampilkan section transparansi di halaman depan',
                'sort_order' => 6,
                'is_public' => true
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
