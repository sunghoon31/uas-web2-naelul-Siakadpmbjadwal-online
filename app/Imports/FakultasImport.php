<?php

namespace App\Imports;

use App\Models\Fakultas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FakultasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Fakultas([
            'nama_fakultas' => $row['nama_fakultas'],
        ]);
    }
}