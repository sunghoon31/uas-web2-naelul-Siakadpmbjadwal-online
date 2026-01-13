<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use Carbon\Carbon;

class FixJamSelesaiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil jadwal + relasi yang BENAR
        $jadwals = Jadwal::with('mataKuliah')->get();

        foreach ($jadwals as $jadwal) {

            // Skip jika tidak ada relasi mata kuliah
            if (!$jadwal->mataKuliah) {
                continue;
            }

            // Asumsi: 1 SKS = 50 menit
            $sks = $jadwal->mataKuliah->sks ?? 0;

            if ($sks <= 0) {
                continue;
            }

            $jamMulai = Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai);
            $jamSelesai = $jamMulai->copy()->addMinutes($sks * 50);

            $jadwal->update([
                'jam_selesai' => $jamSelesai->format('H:i:s'),
            ]);
        }

        $this->command->info('Jam selesai jadwal berhasil diperbaiki.');
    }
}
