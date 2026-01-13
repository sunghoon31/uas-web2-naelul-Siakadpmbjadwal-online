<?php

namespace App\Exports;

use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Prodi::with('fakultas')->get();
    }

    public function headings(): array
    {
        return ['Nama Program Studi', 'Fakultas'];
    }

    public function map($prodi): array
    {
        return [
            $prodi->nama_prodi,
            $prodi->fakultas->nama_fakultas ?? '-',
        ];
    }
}