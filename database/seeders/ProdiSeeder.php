<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prodis')->insert([
            // =====================
            // Fakultas Komputer (PALING BANYAK)
            // =====================
            ['nama_prodi' => 'Sistem Informasi', 'fakultas_id' => 1],
            ['nama_prodi' => 'Teknik Informatika', 'fakultas_id' => 1],
            ['nama_prodi' => 'Manajemen Informatika', 'fakultas_id' => 1],
            ['nama_prodi' => 'Rekayasa Perangkat Lunak', 'fakultas_id' => 1],
            ['nama_prodi' => 'Teknologi Informasi', 'fakultas_id' => 1],

            // =====================
            // Fakultas Teknik
            // =====================
            ['nama_prodi' => 'Teknik Industri', 'fakultas_id' => 2],
            ['nama_prodi' => 'Teknik Mesin', 'fakultas_id' => 2],
            ['nama_prodi' => 'Teknik Elektro', 'fakultas_id' => 2],

            // =====================
            // Fakultas Pertanian (TIDAK disingkat)
            // =====================
            ['nama_prodi' => 'Agroteknologi', 'fakultas_id' => 3],
            ['nama_prodi' => 'Agribisnis', 'fakultas_id' => 3],

            // =====================
            // Fakultas Ekonomi dan Bisnis
            // =====================
            ['nama_prodi' => 'Manajemen', 'fakultas_id' => 4],
            ['nama_prodi' => 'Akuntansi', 'fakultas_id' => 4],

            // =====================
            // Fakultas Hukum
            // =====================
            ['nama_prodi' => 'Ilmu Hukum', 'fakultas_id' => 5],

            // =====================
            // FISIP
            // =====================
            ['nama_prodi' => 'Ilmu Administrasi Negara', 'fakultas_id' => 6],
        ]);
    }
}
