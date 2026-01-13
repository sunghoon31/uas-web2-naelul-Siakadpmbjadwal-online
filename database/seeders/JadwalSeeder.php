<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // RUANGAN PER FAKULTAS
        $ruangans = [
            // Fakultas 1
            ['fakultas_id' => 1, 'kode_ruangan' => 'F1-R101', 'nama_ruangan' => 'Ruang Kelas A1', 'kapasitas' => 40, 'jenis' => 'Kelas'],
            ['fakultas_id' => 1, 'kode_ruangan' => 'F1-R102', 'nama_ruangan' => 'Ruang Kelas B1', 'kapasitas' => 40, 'jenis' => 'Kelas'],
            ['fakultas_id' => 1, 'kode_ruangan' => 'F1-LAB1', 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 30, 'jenis' => 'Lab'],
            
            // Fakultas 2 (FKOM)
            ['fakultas_id' => 2, 'kode_ruangan' => 'FKOM-R201', 'nama_ruangan' => 'Ruang Kelas FKOM A', 'kapasitas' => 40, 'jenis' => 'Kelas'],
            ['fakultas_id' => 2, 'kode_ruangan' => 'FKOM-R202', 'nama_ruangan' => 'Ruang Kelas FKOM B', 'kapasitas' => 40, 'jenis' => 'Kelas'],
            ['fakultas_id' => 2, 'kode_ruangan' => 'FKOM-LAB1', 'nama_ruangan' => 'Lab Komputer FKOM 1', 'kapasitas' => 30, 'jenis' => 'Lab'],
            ['fakultas_id' => 2, 'kode_ruangan' => 'FKOM-LAB2', 'nama_ruangan' => 'Lab Komputer FKOM 2', 'kapasitas' => 30, 'jenis' => 'Lab'],
            ['fakultas_id' => 2, 'kode_ruangan' => 'FKOM-STUDIO', 'nama_ruangan' => 'Studio Multimedia', 'kapasitas' => 25, 'jenis' => 'Studio'],
            
            // Fakultas 3
            ['fakultas_id' => 3, 'kode_ruangan' => 'F3-R301', 'nama_ruangan' => 'Ruang Kelas A3', 'kapasitas' => 35, 'jenis' => 'Kelas'],
            ['fakultas_id' => 3, 'kode_ruangan' => 'F3-R302', 'nama_ruangan' => 'Ruang Kelas B3', 'kapasitas' => 35, 'jenis' => 'Kelas'],
        ];

        foreach ($ruangans as $ruangan) {
            DB::table('ruangans')->insert($ruangan);
        }

        // MATA KULIAH (sesuaikan dengan prodi_id yang ada)
        $mataKuliahs = [
            ['kode_mk' => 'TIF101', 'nama_mk' => 'Pemrograman Web', 'sks' => 3, 'semester' => 1, 'prodi_id' => 2],
            ['kode_mk' => 'TIF102', 'nama_mk' => 'Basis Data', 'sks' => 3, 'semester' => 1, 'prodi_id' => 2],
            ['kode_mk' => 'TIF103', 'nama_mk' => 'Algoritma', 'sks' => 4, 'semester' => 1, 'prodi_id' => 2],
            ['kode_mk' => 'TIF104', 'nama_mk' => 'Jaringan Komputer', 'sks' => 3, 'semester' => 1, 'prodi_id' => 2],
        ];

        foreach ($mataKuliahs as $mk) {
            DB::table('mata_kuliahs')->insert($mk);
        }

        // JADWAL SAMPLE (ruangan FKOM untuk prodi FKOM)
        $jadwals = [
            [
                'mata_kuliah_id' => 1,
                'dosen_id' => 1,
                'ruangan_id' => 4, // FKOM-R201
                'prodi_id' => 2,
                'hari' => 'Senin',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '09:30:00',
                'semester' => 1,
                'status' => 'Aktif',
                'keterangan' => '3 SKS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mata_kuliah_id' => 2,
                'dosen_id' => 2,
                'ruangan_id' => 6, // FKOM-LAB1
                'prodi_id' => 2,
                'hari' => 'Senin',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '12:30:00',
                'semester' => 1,
                'status' => 'Aktif',
                'keterangan' => '3 SKS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jadwals as $jadwal) {
            DB::table('jadwals')->insert($jadwal);
        }
    }
}