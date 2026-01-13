<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalonMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_mahasiswa';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'prodi_id',
        'no_pendaftaran',
        'jalur_masuk',
        'gelombang',
        'status_seleksi',
        'status_verifikasi_berkas',
        'catatan_verifikasi',
        'kartu_ujian',
    ];

    /**
     * IMPORTANT: Append attributes agar accessor otomatis ter-include
     */
    protected $appends = [
        'status_verifikasi_berkas_text',
        'status_verifikasi_badge',
        'jumlah_dokumen',
        'jumlah_dokumen_diverifikasi'
    ];

    /**
     * Relasi ke Prodi
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Relasi ke Dokumen
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenCalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    /**
     * Relasi ke Keuangan PMB
     */
    public function keuanganPMB(): HasMany
    {
        return $this->hasMany(KeuanganPMB::class);
    }

    /**
     * Accessor untuk jenis kelamin
     */
    public function getJenisKelaminLengkapAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Accessor untuk jalur masuk
     */
    public function getJalurMasukNamaAttribute()
    {
        $jalur = [
            'reguler' => 'Reguler',
            'prestasi' => 'Prestasi',
            'beasiswa' => 'Beasiswa',
            'pindahan' => 'Pindahan'
        ];

        return $jalur[$this->jalur_masuk] ?? $this->jalur_masuk;
    }

    /**
     * Accessor untuk status seleksi
     */
    public function getStatusSeleksiTextAttribute()
    {
        $status = [
            'pending' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak'
        ];

        return $status[$this->status_seleksi] ?? $this->status_seleksi;
    }

    /**
     * Accessor untuk status verifikasi berkas - TEXT
     * FIXED: Menggunakan loadCount untuk performa lebih baik
     */
    public function getStatusVerifikasiBerkasTextAttribute()
    {
        // Cek apakah relasi dokumen sudah di-load
        if (!$this->relationLoaded('dokumen')) {
            $this->load('dokumen');
        }
        
        $jumlahDokumen = $this->dokumen->count();
        
        // Jika tidak ada dokumen
        if ($jumlahDokumen == 0) {
            return 'Belum Upload';
        }
        
        // Jika ada field status_verifikasi_berkas di database
        if (!empty($this->attributes['status_verifikasi_berkas'])) {
            $status = [
                'belum_upload' => 'Belum Upload',
                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                'diverifikasi' => 'Diverifikasi',
                'ditolak' => 'Ditolak'
            ];
            return $status[$this->attributes['status_verifikasi_berkas']] ?? 'Menunggu Verifikasi';
        }
        
        // Cek status dokumen untuk menentukan status verifikasi
        $dokumenMenunggu = $this->dokumen->where('status_verifikasi', 'menunggu')->count();
        $dokumenDisetujui = $this->dokumen->where('status_verifikasi', 'disetujui')->count();
        $dokumenDitolak = $this->dokumen->where('status_verifikasi', 'ditolak')->count();
        
        // Jika semua dokumen disetujui
        if ($dokumenDisetujui > 0 && $dokumenDisetujui == $jumlahDokumen) {
            return 'Diverifikasi';
        }
        
        // Jika ada dokumen yang ditolak
        if ($dokumenDitolak > 0) {
            return 'Ditolak';
        }
        
        // Jika ada dokumen menunggu
        if ($dokumenMenunggu > 0) {
            return 'Menunggu Verifikasi';
        }
        
        return 'Menunggu Verifikasi';
    }

    /**
     * Accessor untuk status verifikasi berkas - BADGE
     * FIXED: Menggunakan collection yang sudah di-load
     */
    public function getStatusVerifikasiBadgeAttribute()
    {
        // Cek apakah relasi dokumen sudah di-load
        if (!$this->relationLoaded('dokumen')) {
            $this->load('dokumen');
        }
        
        $jumlahDokumen = $this->dokumen->count();
        
        // Jika tidak ada dokumen
        if ($jumlahDokumen == 0) {
            return 'secondary';
        }
        
        // Jika ada field status_verifikasi_berkas di database
        if (!empty($this->attributes['status_verifikasi_berkas'])) {
            $badges = [
                'belum_upload' => 'secondary',
                'menunggu_verifikasi' => 'warning',
                'diverifikasi' => 'success',
                'ditolak' => 'danger'
            ];
            return $badges[$this->attributes['status_verifikasi_berkas']] ?? 'warning';
        }
        
        // Cek status dokumen untuk menentukan badge
        $dokumenMenunggu = $this->dokumen->where('status_verifikasi', 'menunggu')->count();
        $dokumenDisetujui = $this->dokumen->where('status_verifikasi', 'disetujui')->count();
        $dokumenDitolak = $this->dokumen->where('status_verifikasi', 'ditolak')->count();
        
        // Jika semua dokumen disetujui
        if ($dokumenDisetujui > 0 && $dokumenDisetujui == $jumlahDokumen) {
            return 'success';
        }
        
        // Jika ada dokumen yang ditolak
        if ($dokumenDitolak > 0) {
            return 'danger';
        }
        
        // Jika ada dokumen menunggu
        if ($dokumenMenunggu > 0) {
            return 'warning';
        }
        
        return 'warning';
    }

    /**
     * Get jumlah total dokumen
     */
    public function getJumlahDokumenAttribute()
    {
        // Cek apakah relasi dokumen sudah di-load
        if (!$this->relationLoaded('dokumen')) {
            $this->load('dokumen');
        }
        
        return $this->dokumen->count();
    }

    /**
     * Get jumlah dokumen yang sudah diverifikasi
     */
    public function getJumlahDokumenDiverifikasiAttribute()
    {
        // Cek apakah relasi dokumen sudah di-load
        if (!$this->relationLoaded('dokumen')) {
            $this->load('dokumen');
        }
        
        return $this->dokumen->where('status_verifikasi', 'disetujui')->count();
    }

    /**
     * Check apakah semua dokumen sudah diverifikasi
     */
    public function isAllDokumenVerified()
    {
        $totalDokumen = $this->dokumen()->count();
        
        if ($totalDokumen === 0) {
            return false;
        }
        
        $dokumenVerified = $this->dokumen()
            ->where('status_verifikasi', 'disetujui')
            ->count();
        
        return $totalDokumen === $dokumenVerified;
    }

    /**
     * Get URL kartu ujian
     */
    public function getKartuUjianUrlAttribute()
    {
        if ($this->kartu_ujian) {
            return asset('storage/' . $this->kartu_ujian);
        }
        return null;
    }

    /**
     * Get total biaya PMB
     */
    public function getTotalBiayaAttribute()
    {
        return $this->keuanganPMB()->sum('nominal');
    }

    /**
     * Get total yang sudah dibayar
     */
    public function getTotalBayarAttribute()
    {
        return $this->keuanganPMB()
            ->whereIn('status_bayar', ['sudah_bayar', 'dibebaskan'])
            ->sum('nominal');
    }

    /**
     * Get sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        $total = $this->keuanganPMB()->sum('nominal');
        $bayar = $this->keuanganPMB()
            ->whereIn('status_bayar', ['sudah_bayar', 'dibebaskan'])
            ->sum('nominal');
        
        return $total - $bayar;
    }

    /**
     * Check apakah semua biaya sudah lunas
     */
    public function isLunas()
    {
        $belumBayar = $this->keuanganPMB()
            ->where('status_bayar', 'belum_bayar')
            ->count();
        
        return $belumBayar === 0;
    }
}