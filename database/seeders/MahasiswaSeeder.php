<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $namaMahasiswa = [
            'Ahmad Fauzi',
            'Siti Aisyah',
            'Muhammad Rizki',
            'Nurul Hidayah',
            'Dimas Pratama',
            'Putri Ramadhani',
            'Rizal Maulana',
            'Anisa Fitriani',
            'Fajar Nugraha',
            'Lutfi Hakim',
            'Aulia Rahman',
            'Nabila Safitri',
            'Ilham Saputra',
            'Dewi Lestari',
            'Randi Kurniawan',
            'Yuni Kartika',
            'Bagas Aditya',
            'Salma Nur Azizah',
            'Arif Setiawan',
            'Intan Permata'
        ];

        $data = [];

        foreach ($namaMahasiswa as $i => $nama) {
            $data[] = [
                'nim'        => '20220' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'nama'       => $nama,
                'angkatan'   => rand(2020, 2023),
                'prodi_id'   => rand(1, 14),
                'foto'       => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('mahasiswa')->insert($data);
    }
}
