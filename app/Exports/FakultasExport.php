<?php

namespace App\Exports;

use App\Models\Fakultas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FakultasExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Fakultas::withCount('prodis')->get();
    }

    public function headings(): array
    {
        return ['Nama Fakultas', 'Jumlah Prodi'];
    }

    public function map($fakultas): array
    {
        return [
            $fakultas->nama_fakultas,
            $fakultas->prodis_count,
        ];
    }
}