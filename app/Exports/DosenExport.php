<?php

namespace App\Exports;

use App\Models\Dosen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DosenExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Dosen::with('prodi.fakultas')->get();
    }

    public function headings(): array
    {
        return [
            'NIDN',
            'Nama',
            'Email',
            'No HP',
            'Program Studi',
            'Fakultas',
        ];
    }

    public function map($dosen): array
    {
        return [
            $dosen->nidn,
            $dosen->nama,
            $dosen->email,
            $dosen->no_hp,
            $dosen->prodi->nama_prodi ?? '-',
            $dosen->prodi->fakultas->nama_fakultas ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}