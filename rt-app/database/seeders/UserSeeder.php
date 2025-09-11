<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@rt.local'],
            [
                'name' => 'Admin RT',
                'email' => 'admin@rt.local',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Create regular user
        User::updateOrCreate(
            ['email' => 'user@rt.local'],
            [
                'name' => 'Regular User',
                'email' => 'user@rt.local',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );
    }
}
