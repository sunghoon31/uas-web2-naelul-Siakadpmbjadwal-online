<?php

namespace App\Imports;

use App\Models\Prodi;
use App\Models\Fakultas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $fakultas = Fakultas::where('nama_fakultas', 'LIKE', '%' . $row['fakultas'] . '%')->first();

        return new Prodi([
            'nama_prodi' => $row['nama_program_studi'],
            'fakultas_id' => $fakultas ? $fakultas->id : null,
        ]);
    }
}