<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalonMahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $calonMahasiswa;

    public function __construct($calonMahasiswa)
    {
        $this->calonMahasiswa = $calonMahasiswa;
    }

    public function collection()
    {
        return $this->calonMahasiswa;
    }

    public function headings(): array
    {
        return [
            'No',
            'No Pendaftaran',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Alamat',
            'No HP',
            'Program Studi',
            'Fakultas',
            'Jalur Masuk',
            'Gelombang',
            'Status Seleksi',
            'Status Verifikasi Berkas',
            'Tanggal Daftar'
        ];
    }

    public function map($calonMahasiswa): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $calonMahasiswa->no_pendaftaran,
            $calonMahasiswa->nama,
            $calonMahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $calonMahasiswa->alamat,
            $calonMahasiswa->no_hp,
            $calonMahasiswa->prodi ? $calonMahasiswa->prodi->nama_prodi : '-',
            $calonMahasiswa->prodi && $calonMahasiswa->prodi->fakultas ? $calonMahasiswa->prodi->fakultas->nama_fakultas : '-',
            ucfirst($calonMahasiswa->jalur_masuk),
            $calonMahasiswa->gelombang ?? '-',
            ucfirst($calonMahasiswa->status_seleksi),
            $this->getStatusVerifikasiBerkas($calonMahasiswa->status_verifikasi_berkas),
            $calonMahasiswa->created_at ? $calonMahasiswa->created_at->format('d/m/Y H:i') : '-'
        ];
    }

    private function getStatusVerifikasiBerkas($status)
    {
        $statusMap = [
            'belum_upload' => 'Belum Upload',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak'
        ];

        return $statusMap[$status] ?? 'Belum Upload';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // No Pendaftaran
            'C' => 25,  // Nama
            'D' => 15,  // Jenis Kelamin
            'E' => 35,  // Alamat
            'F' => 15,  // No HP
            'G' => 30,  // Prodi
            'H' => 25,  // Fakultas
            'I' => 15,  // Jalur Masuk
            'J' => 15,  // Gelombang
            'K' => 15,  // Status Seleksi
            'L' => 20,  // Status Verifikasi
            'M' => 18,  // Tanggal Daftar
        ];
    }
}