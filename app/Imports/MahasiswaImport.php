<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari prodi by nama
        $prodi = Prodi::where('nama_prodi', 'LIKE', '%' . $row['program_studi'] . '%')->first();

        return new Mahasiswa([
            'nim' => $row['nim'],
            'nama' => $row['nama'],
            'angkatan' => $row['angkatan'],
            'prodi_id' => $prodi ? $prodi->id : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|unique:mahasiswa,nim',
            'nama' => 'required|string',
            'angkatan' => 'required|numeric',
            'program_studi' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'angkatan.required' => 'Angkatan wajib diisi',
            'angkatan.numeric' => 'Angkatan harus berupa angka',
            'program_studi.required' => 'Program Studi wajib diisi',
        ];
    }
}