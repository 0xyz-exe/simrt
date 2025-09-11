<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Resident;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residents = Resident::all();

        $businesses = [
            [
                'name' => 'Warung Makan Bu Sari',
                'description' => 'Warung makan rumahan dengan menu masakan Padang dan Jawa. Tersedia berbagai lauk pauk dengan harga terjangkau dan cita rasa yang lezat. Buka setiap hari dari pagi hingga malam.',
                'owner_name' => 'Ibu Sari Dewi',
                'category' => 'kuliner',
                'address' => 'Jl. Melati No. 15, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567890',
                'whatsapp' => '081234567890',
                'email' => 'warungbusari@gmail.com',
                'instagram' => '@warungbusari',
                'operating_hours' => [
                    'senin' => '06:00 - 21:00',
                    'selasa' => '06:00 - 21:00',
                    'rabu' => '06:00 - 21:00',
                    'kamis' => '06:00 - 21:00',
                    'jumat' => '06:00 - 21:00',
                    'sabtu' => '06:00 - 21:00',
                    'minggu' => '06:00 - 21:00'
                ],
                'is_active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Toko Kelontong Berkah',
                'description' => 'Toko kelontong lengkap yang menyediakan kebutuhan sehari-hari seperti beras, minyak, gula, telur, dan berbagai keperluan rumah tangga lainnya dengan harga kompetitif.',
                'owner_name' => 'Bapak Ahmad Supandi',
                'category' => 'lainnya',
                'address' => 'Jl. Melati No. 22, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567891',
                'whatsapp' => '081234567891',
                'operating_hours' => [
                    'senin' => '05:00 - 22:00',
                    'selasa' => '05:00 - 22:00',
                    'rabu' => '05:00 - 22:00',
                    'kamis' => '05:00 - 22:00',
                    'jumat' => '05:00 - 22:00',
                    'sabtu' => '05:00 - 22:00',
                    'minggu' => '05:00 - 22:00'
                ],
                'is_active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Salon Cantik Indah',
                'description' => 'Salon kecantikan untuk wanita dengan layanan potong rambut, cat rambut, creambath, facial, dan perawatan kuku. Dilayani oleh tenaga ahli berpengalaman.',
                'owner_name' => 'Ibu Indah Permata',
                'category' => 'kesehatan',
                'address' => 'Jl. Melati No. 8, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567892',
                'whatsapp' => '081234567892',
                'instagram' => '@saloncantikindah',
                'facebook' => 'Salon Cantik Indah',
                'operating_hours' => [
                    'senin' => 'Tutup',
                    'selasa' => '09:00 - 18:00',
                    'rabu' => '09:00 - 18:00',
                    'kamis' => '09:00 - 18:00',
                    'jumat' => '09:00 - 18:00',
                    'sabtu' => '08:00 - 19:00',
                    'minggu' => '08:00 - 17:00'
                ],
                'is_active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Bengkel Motor Jaya',
                'description' => 'Bengkel motor untuk servis rutin, ganti oli, perbaikan mesin, dan spare part motor. Melayani semua jenis motor dengan harga terjangkau dan pelayanan memuaskan.',
                'owner_name' => 'Bapak Joko Susanto',
                'category' => 'otomotif',
                'address' => 'Jl. Melati No. 30, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567893',
                'whatsapp' => '081234567893',
                'operating_hours' => [
                    'senin' => '08:00 - 17:00',
                    'selasa' => '08:00 - 17:00',
                    'rabu' => '08:00 - 17:00',
                    'kamis' => '08:00 - 17:00',
                    'jumat' => '08:00 - 17:00',
                    'sabtu' => '08:00 - 15:00',
                    'minggu' => 'Tutup'
                ],
                'is_active' => true,
                'featured' => false,
            ],
            [
                'name' => 'Kerajinan Bambu Kreatif',
                'description' => 'Usaha kerajinan tangan dari bambu seperti keranjang, tempat pensil, hiasan dinding, dan berbagai souvenir unik. Menerima pesanan custom sesuai keinginan.',
                'owner_name' => 'Ibu Tri Wahyuni',
                'category' => 'kerajinan',
                'address' => 'Jl. Melati No. 12, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567894',
                'whatsapp' => '081234567894',
                'instagram' => '@kerajinanbambukreatif',
                'email' => 'bambukreatif@gmail.com',
                'operating_hours' => [
                    'senin' => '08:00 - 16:00',
                    'selasa' => '08:00 - 16:00',
                    'rabu' => '08:00 - 16:00',
                    'kamis' => '08:00 - 16:00',
                    'jumat' => '08:00 - 16:00',
                    'sabtu' => '08:00 - 14:00',
                    'minggu' => 'Tutup'
                ],
                'is_active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Laundry Express 24',
                'description' => 'Jasa laundry kiloan dan satuan dengan layanan antar jemput. Tersedia layanan cuci kering, setrika, dan dry cleaning untuk berbagai jenis pakaian.',
                'owner_name' => 'Bapak Andi Kurniawan',
                'category' => 'jasa',
                'address' => 'Jl. Melati No. 25, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567895',
                'whatsapp' => '081234567895',
                'operating_hours' => [
                    'senin' => '07:00 - 20:00',
                    'selasa' => '07:00 - 20:00',
                    'rabu' => '07:00 - 20:00',
                    'kamis' => '07:00 - 20:00',
                    'jumat' => '07:00 - 20:00',
                    'sabtu' => '07:00 - 20:00',
                    'minggu' => '08:00 - 18:00'
                ],
                'is_active' => true,
                'featured' => false,
            ],
            [
                'name' => 'Toko Baju Muslimah Ayu',
                'description' => 'Toko pakaian muslimah dengan berbagai model hijab, gamis, tunik, dan aksesoris muslimah. Koleksi terbaru dengan kualitas bahan yang nyaman dan harga terjangkau.',
                'owner_name' => 'Ibu Ayu Lestari',
                'category' => 'fashion',
                'address' => 'Jl. Melati No. 18, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567896',
                'whatsapp' => '081234567896',
                'instagram' => '@bajumuslimahayu',
                'facebook' => 'Toko Baju Muslimah Ayu',
                'operating_hours' => [
                    'senin' => '09:00 - 17:00',
                    'selasa' => '09:00 - 17:00',
                    'rabu' => '09:00 - 17:00',
                    'kamis' => '09:00 - 17:00',
                    'jumat' => '09:00 - 17:00',
                    'sabtu' => '09:00 - 18:00',
                    'minggu' => '10:00 - 16:00'
                ],
                'is_active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Service HP & Komputer',
                'description' => 'Jasa service dan reparasi handphone, laptop, komputer, dan gadget elektronik lainnya. Menyediakan spare part original dan garansi untuk setiap perbaikan.',
                'owner_name' => 'Bapak Dedi Prasetyo',
                'category' => 'elektronik',
                'address' => 'Jl. Melati No. 35, RT 02/RW 04 Kedurus - Kepatihan',
                'phone' => '081234567897',
                'whatsapp' => '081234567897',
                'email' => 'servicehpkomputer@gmail.com',
                'operating_hours' => [
                    'senin' => '09:00 - 18:00',
                    'selasa' => '09:00 - 18:00',
                    'rabu' => '09:00 - 18:00',
                    'kamis' => '09:00 - 18:00',
                    'jumat' => '09:00 - 18:00',
                    'sabtu' => '09:00 - 16:00',
                    'minggu' => 'Tutup'
                ],
                'is_active' => true,
                'featured' => false,
            ]
        ];

        foreach ($businesses as $index => $businessData) {
            // Assign random resident if exists
            if ($residents->count() > 0) {
                $businessData['resident_id'] = $residents->random()->id;
            }

            Business::create($businessData);
        }
    }
}
