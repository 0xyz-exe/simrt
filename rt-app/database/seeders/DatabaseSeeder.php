<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed core data and test users
        $this->call([
            UserSeeder::class,
            // Optionally add other seeders below as needed
            // SettingSeeder::class,
            // BusinessSeeder::class,
            // ActivitySeeder::class,
            // AnnouncementSeeder::class,
            // SampleDataSeeder::class,
        ]);
    }
}
