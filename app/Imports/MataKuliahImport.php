<?php

namespace App\Imports;

use App\Models\MataKuliah;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $prodi = Prodi::where('nama_prodi', 'LIKE', '%'.$row['program_studi'].'%')->first();

        return new MataKuliah([
            'kode_mk' => $row['kode_mk'],
            'nama_mk' => $row['nama_mata_kuliah'],
            'sks' => $row['sks'],
            'semester' => $row['semester'],
            'prodi_id' => $prodi->id ?? null,
        ]);
    }
}