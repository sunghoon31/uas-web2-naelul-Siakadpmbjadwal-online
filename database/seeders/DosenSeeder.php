<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $namaDosen = [
            'Dr. H. Budi Santoso, M.Kom',
            'Dr. Siti Nurhayati, M.Si',
            'Ir. Ahmad Yani, M.T',
            'Dr. Rina Marlina, M.Kom',
            'Dr. Andi Wijaya, M.Sc',
            'Dr. Lilis Kurniawati, M.Pd',
            'Ir. Dedi Supriatna, M.T',
            'Dr. Hendra Gunawan, M.Kom',
            'Dr. Yuni Astuti, M.Si',
            'Dr. Rudi Hartono, M.T',
            'Dr. Fitri Handayani, M.Kom',
            'Dr. Agus Salim, M.Sc',
            'Dr. Nani Suryani, M.Pd',
            'Dr. Eko Prasetyo, M.Kom',
            'Dr. Maya Sari, M.Si',
            'Ir. Wahyu Setiawan, M.T',
            'Dr. Lina Marlina, M.Kom',
            'Dr. Toni Firmansyah, M.Sc',
            'Dr. Sri Wahyuni, M.Pd',
            'Dr. Aditya Putra, M.Kom'
        ];

        $data = [];

        foreach ($namaDosen as $i => $nama) {
            $data[] = [
                'nama'       => $nama,
                'nidn'       => '04123' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'email'      => 'dosen' . ($i + 1) . '@kampus.ac.id',
                'no_hp'      => '08' . rand(1000000000, 9999999999),
                'prodi_id'   => $i % 3 === 0 ? null : rand(1, 14), // TIDAK semua punya homebase
                'foto'       => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('dosen')->insert($data);
    }
}
