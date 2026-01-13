<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // MAHASISWA
        User::updateOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Mahasiswa',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
            ]
        );
    }
}
