<?php

namespace App\Exports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MataKuliahExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MataKuliah::with('prodi')->get();
    }

    public function headings(): array
    {
        return ['Kode MK', 'Nama Mata Kuliah', 'SKS', 'Semester', 'Program Studi'];
    }

    public function map($mataKuliah): array
    {
        return [
            $mataKuliah->kode_mk,
            $mataKuliah->nama_mk,
            $mataKuliah->sks,
            $mataKuliah->semester,
            $mataKuliah->prodi->nama_prodi ?? '-',
        ];
    }
}