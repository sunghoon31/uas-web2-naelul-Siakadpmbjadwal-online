<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DokumenCalonMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_calon_mahasiswa';

    protected $fillable = [
        'calon_mahasiswa_id',
        'jenis_dokumen',
        'nama_file',
        'path_file',
        'original_name',
        'ukuran_file',
        'mime_type',
        'status_verifikasi',
        'catatan',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi ke Calon Mahasiswa
     */
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class);
    }

    /**
     * Relasi ke User yang memverifikasi
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get URL file
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->path_file);
    }

    /**
     * Get ukuran file dalam format readable
     */
    public function getUkuranFileBytesAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->ukuran_file;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get nama jenis dokumen
     */
    public function getJenisDokumenNamaAttribute()
    {
        $jenis = [
            'ijazah' => 'Ijazah',
            'transkrip_nilai' => 'Transkrip Nilai',
            'kartu_keluarga' => 'Kartu Keluarga',
            'akta_kelahiran' => 'Akta Kelahiran',
            'foto_diri' => 'Foto Diri',
            'surat_keterangan_sehat' => 'Surat Keterangan Sehat',
            'surat_kelakuan_baik' => 'Surat Kelakuan Baik',
            'sertifikat_prestasi' => 'Sertifikat Prestasi',
            'surat_rekomendasi' => 'Surat Rekomendasi',
            'lainnya' => 'Lainnya'
        ];

        return $jenis[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $badges = [
            'menunggu' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger'
        ];

        return $badges[$this->status_verifikasi] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $status = [
            'menunggu' => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak'
        ];

        return $status[$this->status_verifikasi] ?? $this->status_verifikasi;
    }

    /**
     * Check if file exists
     */
    public function fileExists()
    {
        return Storage::exists($this->path_file);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile()
    {
        if ($this->fileExists()) {
            Storage::delete($this->path_file);
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Hapus file saat model dihapus
        static::deleting(function ($dokumen) {
            $dokumen->deleteFile();
        });
    }
}