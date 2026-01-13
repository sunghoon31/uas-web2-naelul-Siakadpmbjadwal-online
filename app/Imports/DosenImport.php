<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DosenImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari prodi by nama
        $prodi = Prodi::where('nama_prodi', 'LIKE', '%' . $row['program_studi'] . '%')->first();

        return new Dosen([
            'nidn' => $row['nidn'],
            'nama' => $row['nama'],
            'email' => $row['email'],
            'no_hp' => $row['no_hp'],
            'prodi_id' => $prodi ? $prodi->id : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nidn' => 'required|unique:dosen,nidn',
            'nama' => 'required|string',
            'email' => 'required|email|unique:dosen,email',
            'no_hp' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nidn.required' => 'NIDN wajib diisi',
            'nidn.unique' => 'NIDN sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'no_hp.required' => 'No HP wajib diisi',
        ];
    }
}