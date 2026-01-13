<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeuanganPMB extends Model
{
    use HasFactory;

    protected $table = 'keuangan_pmb';

    protected $fillable = [
        'calon_mahasiswa_id',
        'jenis_biaya',
        'nominal',
        'keterangan',
        'status_bayar',
        'tanggal_bayar',
        'metode_bayar',
        'bukti_bayar',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    /**
     * Relasi ke Calon Mahasiswa
     */
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class);
    }

    /**
     * Accessor untuk nama jenis biaya
     */
    public function getJenisBiayaNamaAttribute()
    {
        $names = [
            'formulir' => 'Biaya Formulir',
            'ujian' => 'Biaya Seleksi/Ujian',
            'daftar_ulang' => 'Biaya Daftar Ulang Awal'
        ];

        return $names[$this->jenis_biaya] ?? $this->jenis_biaya;
    }

    /**
     * Accessor untuk status pembayaran
     */
    public function getStatusBayarTextAttribute()
    {
        $status = [
            'belum_bayar' => 'Belum Bayar',
            'sudah_bayar' => 'Sudah Bayar',
            'dibebaskan' => 'Dibebaskan'
        ];

        return $status[$this->status_bayar] ?? $this->status_bayar;
    }

    /**
     * Scope untuk filter sudah bayar
     */
    public function scopeSudahBayar($query)
    {
        return $query->where('status_bayar', 'sudah_bayar');
    }

    /**
     * Scope untuk filter belum bayar
     */
    public function scopeBelumBayar($query)
    {
        return $query->where('status_bayar', 'belum_bayar');
    }

    /**
     * Check apakah sudah bayar
     */
    public function isBayar()
    {
        return in_array($this->status_bayar, ['sudah_bayar', 'dibebaskan']);
    }
}