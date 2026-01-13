<?php

namespace App\Exports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class RuanganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Ruangan::with('fakultas')->orderBy('fakultas_id')->orderBy('kode_ruangan')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Ruangan',
            'Nama Ruangan',
            'Fakultas',
            'Jenis',
            'Kapasitas',
            'Tanggal Dibuat'
        ];
    }

    /**
     * @param mixed $ruangan
     * @return array
     */
    public function map($ruangan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $ruangan->kode_ruangan,
            $ruangan->nama_ruangan,
            $ruangan->fakultas ? $ruangan->fakultas->nama_fakultas : '-',
            $ruangan->jenis,
            $ruangan->kapasitas . ' orang',
            $ruangan->created_at ? $ruangan->created_at->format('d-m-Y H:i') : '-'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667EEA']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ],
            
            // Style untuk semua cell
            'A:G' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 35,
            'E' => 15,
            'F' => 15,
            'G' => 20
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Ruangan';
    }
}