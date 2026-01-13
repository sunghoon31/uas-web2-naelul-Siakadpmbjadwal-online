<?php

namespace App\Imports;

use App\Models\Ruangan;
use App\Models\Fakultas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class RuanganImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cari fakultas berdasarkan nama
        $fakultas = Fakultas::where('nama_fakultas', 'like', '%' . $row['fakultas'] . '%')->first();
        
        if (!$fakultas) {
            throw new \Exception('Fakultas "' . $row['fakultas'] . '" tidak ditemukan');
        }

        // Cek apakah kode ruangan sudah ada
        $existingRuangan = Ruangan::where('kode_ruangan', $row['kode_ruangan'])->first();
        
        if ($existingRuangan) {
            // Update jika sudah ada
            $existingRuangan->update([
                'fakultas_id' => $fakultas->id,
                'nama_ruangan' => $row['nama_ruangan'],
                'kapasitas' => $row['kapasitas'],
                'jenis' => $row['jenis']
            ]);
            
            return null;
        }

        // Buat baru jika belum ada
        return new Ruangan([
            'fakultas_id' => $fakultas->id,
            'kode_ruangan' => $row['kode_ruangan'],
            'nama_ruangan' => $row['nama_ruangan'],
            'kapasitas' => $row['kapasitas'],
            'jenis' => $row['jenis']
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'kode_ruangan' => 'required|string|max:255',
            'nama_ruangan' => 'required|string|max:255',
            'fakultas' => 'required|string',
            'kapasitas' => 'required|integer|min:1|max:500',
            'jenis' => 'required|in:Kelas,Lab,Studio'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'kode_ruangan.required' => 'Kode ruangan harus diisi',
            'nama_ruangan.required' => 'Nama ruangan harus diisi',
            'fakultas.required' => 'Fakultas harus diisi',
            'kapasitas.required' => 'Kapasitas harus diisi',
            'kapasitas.integer' => 'Kapasitas harus berupa angka',
            'kapasitas.min' => 'Kapasitas minimal 1 orang',
            'kapasitas.max' => 'Kapasitas maksimal 500 orang',
            'jenis.required' => 'Jenis ruangan harus diisi',
            'jenis.in' => 'Jenis ruangan harus Kelas, Lab, atau Studio'
        ];
    }

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }
}