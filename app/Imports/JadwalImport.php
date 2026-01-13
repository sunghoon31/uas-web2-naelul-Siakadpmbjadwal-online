<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JadwalImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari data berdasarkan nama atau kode
        $mataKuliah = MataKuliah::where('nama_mk', $row['mata_kuliah'])
            ->orWhere('kode_mk', $row['kode_mk'])
            ->first();

        $dosen = Dosen::where('nama', $row['dosen'])->first();
        $ruangan = Ruangan::where('nama_ruangan', $row['ruangan'])->first();
        $prodi = Prodi::where('nama_prodi', $row['prodi'])->first();

        if (!$mataKuliah || !$dosen || !$ruangan || !$prodi) {
            throw new \Exception("Data tidak ditemukan untuk baris: " . json_encode($row));
        }

        return new Jadwal([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $dosen->id,
            'ruangan_id' => $ruangan->id,
            'prodi_id' => $prodi->id,
            'hari' => $row['hari'],
            'jam_mulai' => $row['jam_mulai'],
            'jam_selesai' => $row['jam_selesai'],
            'semester' => (int) str_replace('Semester ', '', $row['semester']),
            'keterangan' => $row['keterangan'] ?? null,
            'status' => 'Aktif'
        ]);
    }

    public function rules(): array
    {
        return [
            '*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            '*.jam_mulai' => 'required',
            '*.jam_selesai' => 'required',
            '*.mata_kuliah' => 'required',
            '*.dosen' => 'required',
            '*.ruangan' => 'required',
            '*.prodi' => 'required',
            '*.semester' => 'required',
        ];
    }
}