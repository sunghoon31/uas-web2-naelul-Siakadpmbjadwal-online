<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Mahasiswa::with('prodi.fakultas')->get();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Angkatan',
            'Program Studi',
            'Fakultas',
        ];
    }

    public function map($mahasiswa): array
    {
        return [
            $mahasiswa->nim,
            $mahasiswa->nama,
            $mahasiswa->angkatan,
            $mahasiswa->prodi->nama_prodi ?? '-',
            $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}