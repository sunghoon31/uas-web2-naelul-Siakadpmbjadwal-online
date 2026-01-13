namespace App\Exports;

use App\Models\KeuanganPMB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KeuanganPMBExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return KeuanganPMB::with('calonMahasiswa.prodi')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Nama',
            'Prodi',
            'Jenis Biaya',
            'Nominal',
            'Status Bayar',
            'Tanggal Bayar',
            'Metode Bayar',
        ];
    }

    public function map($keuangan): array
    {
        return [
            $keuangan->calonMahasiswa->no_pendaftaran,
            $keuangan->calonMahasiswa->nama,
            $keuangan->calonMahasiswa->prodi->nama_prodi,
            $keuangan->jenis_biaya_nama,
            'Rp ' . number_format($keuangan->nominal, 0, ',', '.'),
            $keuangan->status_bayar_text,
            $keuangan->tanggal_bayar ? $keuangan->tanggal_bayar->format('d/m/Y') : '-',
            $keuangan->metode_bayar ?? '-',
        ];
    }
}