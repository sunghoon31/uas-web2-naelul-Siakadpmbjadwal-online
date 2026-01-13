<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fakultas')->insert([
            ['nama_fakultas' => 'Fakultas Komputer'],
            ['nama_fakultas' => 'Fakultas Teknik'],
            ['nama_fakultas' => 'Fakultas Pertanian'],
            ['nama_fakultas' => 'Fakultas Ekonomi dan Bisnis'],
            ['nama_fakultas' => 'Fakultas Hukum'],
            ['nama_fakultas' => 'Fakultas Ilmu Sosial dan Politik'],
        ]);
    }
}
