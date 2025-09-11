<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        // Data demografi untuk demo - dalam implementasi nyata, ini akan dari database
        $demographicData = [
            'total_residents' => 127,
            'total_families' => 35,
            'total_houses' => 35,
            'population_density' => 51, // per hektar

            // Data Usia
            'age_groups' => [
                '0-5' => 8,
                '6-17' => 22,
                '18-25' => 18,
                '26-40' => 35,
                '41-60' => 32,
                '60+' => 12
            ],

            // Data Jenis Kelamin
            'gender' => [
                'male' => 64,
                'female' => 63
            ],

            // Data Pekerjaan
            'occupations' => [
                'PNS' => 15,
                'Karyawan Swasta' => 28,
                'Wiraswasta' => 22,
                'Mahasiswa/Pelajar' => 25,
                'Ibu Rumah Tangga' => 18,
                'Pensiunan' => 8,
                'Tidak Bekerja' => 11
            ],

            // Data Pendidikan
            'education' => [
                'SD' => 12,
                'SMP' => 18,
                'SMA/SMK' => 45,
                'Diploma' => 15,
                'S1' => 28,
                'S2/S3' => 9
            ],

            // Data Status Pernikahan
            'marital_status' => [
                'Belum Menikah' => 42,
                'Menikah' => 73,
                'Cerai' => 8,
                'Janda/Duda' => 4
            ],

            // Data Kepemilikan Rumah
            'house_ownership' => [
                'Milik Sendiri' => 28,
                'Kontrak' => 5,
                'Sewa' => 2
            ],

            // Data Kendaraan
            'vehicles' => [
                'Mobil' => 22,
                'Motor' => 58,
                'Sepeda' => 35,
                'Tidak Ada' => 8
            ]
        ];

        // Perhitungan statistik tambahan
        $statistics = [
            'avg_family_size' => round($demographicData['total_residents'] / $demographicData['total_families'], 1),
            'children_percentage' => round(($demographicData['age_groups']['0-5'] + $demographicData['age_groups']['6-17']) / $demographicData['total_residents'] * 100, 1),
            'working_age_percentage' => round(($demographicData['age_groups']['18-25'] + $demographicData['age_groups']['26-40'] + $demographicData['age_groups']['41-60']) / $demographicData['total_residents'] * 100, 1),
            'elderly_percentage' => round($demographicData['age_groups']['60+'] / $demographicData['total_residents'] * 100, 1),
            'employment_rate' => round((array_sum($demographicData['occupations']) - $demographicData['occupations']['Tidak Bekerja'] - $demographicData['occupations']['Mahasiswa/Pelajar']) / $demographicData['total_residents'] * 100, 1)
        ];

        return view('landing.statistics', compact('demographicData', 'statistics'));
    }

    public function apiData()
    {
        // API endpoint untuk data real-time (untuk chart updates)
        return response()->json([
            'timestamp' => now(),
            'total_residents' => 127,
            'new_residents_this_month' => 2,
            'moved_out_this_month' => 1
        ]);
    }
}
