<?php

namespace App\Exports;

use App\Models\Jadwal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalExport implements FromCollection, WithHeadings, WithMapping
{
    protected $prodiId;
    protected $semester;

    public function __construct($prodiId, $semester)
    {
        $this->prodiId = $prodiId;
        $this->semester = $semester;
    }

    public function collection()
    {
        return Jadwal::with(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
            ->where('prodi_id', $this->prodiId)
            ->where('semester', $this->semester)
            ->where('status', 'Aktif')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Mata Kuliah',
            'Kode MK',
            'Dosen',
            'Ruangan',
            'Kode Ruangan',
            'Semester',
            'Keterangan'
        ];
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->hari,
            $jadwal->jam_mulai,
            $jadwal->jam_selesai,
            $jadwal->mataKuliah->nama_mk,
            $jadwal->mataKuliah->kode_mk,
            $jadwal->dosen->nama,
            $jadwal->ruangan->nama_ruangan,
            $jadwal->ruangan->kode_ruangan,
            'Semester ' . $jadwal->semester,
            $jadwal->keterangan ?? '-'
        ];
    }
}