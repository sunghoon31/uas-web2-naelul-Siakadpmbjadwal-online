<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'ruangan_id',
        'prodi_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    /**
     * Relasi ke Mata Kuliah
     */
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Relasi ke Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Relasi ke Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    /**
     * Scope untuk cek bentrok jadwal - DIPERBAIKI
     * Mengecek overlap waktu dengan lebih akurat
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $hari
     * @param string $jamMulai
     * @param string $jamSelesai
     * @param int|null $excludeId ID jadwal yang dikecualikan (untuk update)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCekBentrok($query, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $query->where('hari', $hari)
              ->where('status', 'Aktif')
              ->where(function($q) use ($jamMulai, $jamSelesai) {
                  // Kondisi 1: Jadwal baru mulai di antara jadwal existing
                  $q->where(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '<', $jamSelesai)
                         ->where('jam_mulai', '>=', $jamMulai);
                  })
                  // Kondisi 2: Jadwal baru selesai di antara jadwal existing
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_selesai', '>', $jamMulai)
                         ->where('jam_selesai', '<=', $jamSelesai);
                  })
                  // Kondisi 3: Jadwal baru menutupi seluruh jadwal existing
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '>=', $jamMulai)
                         ->where('jam_selesai', '<=', $jamSelesai);
                  })
                  // Kondisi 4: Jadwal existing menutupi seluruh jadwal baru
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '<=', $jamMulai)
                         ->where('jam_selesai', '>=', $jamSelesai);
                  });
              });

        // Exclude jadwal tertentu (untuk update)
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    /**
     * Cek bentrok untuk prodi tertentu di semester tertentu
     * Memastikan tidak ada jadwal yang overlap dalam satu prodi-semester
     */
    public function scopeCekBentrokProdi($query, $prodiId, $semester, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        return $query->cekBentrok($hari, $jamMulai, $jamSelesai, $excludeId)
                     ->where('prodi_id', $prodiId)
                     ->where('semester', $semester);
    }

    /**
     * Cek bentrok untuk dosen
     */
    public function scopeCekBentrokDosen($query, $dosenId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        return $query->cekBentrok($hari, $jamMulai, $jamSelesai, $excludeId)
                     ->where('dosen_id', $dosenId);
    }

    /**
     * Cek bentrok untuk ruangan
     */
    public function scopeCekBentrokRuangan($query, $ruanganId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        return $query->cekBentrok($hari, $jamMulai, $jamSelesai, $excludeId)
                     ->where('ruangan_id', $ruanganId);
    }

    /**
     * BARU: Cek apakah mata kuliah sudah ada di hari yang sama dalam 1 prodi
     * Mencegah mata kuliah yang sama dijadwalkan 2 kali dalam sehari di prodi yang sama
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $mataKuliahId
     * @param int $prodiId
     * @param int $semester
     * @param string $hari
     * @param int|null $excludeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCekMataKuliahDuplikat($query, $mataKuliahId, $prodiId, $semester, $hari, $excludeId = null)
    {
        $query->where('mata_kuliah_id', $mataKuliahId)
              ->where('prodi_id', $prodiId)
              ->where('semester', $semester)
              ->where('hari', $hari)
              ->where('status', 'Aktif');

        // Exclude jadwal tertentu (untuk update)
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    /**
     * Accessor untuk format waktu
     */
    public function getWaktuAttribute()
    {
        return date('H:i', strtotime($this->jam_mulai)) . ' - ' . date('H:i', strtotime($this->jam_selesai));
    }

    /**
     * Accessor untuk durasi dalam menit
     */
    public function getDurasiMenitAttribute()
    {
        $mulai = strtotime($this->jam_mulai);
        $selesai = strtotime($this->jam_selesai);
        return ($selesai - $mulai) / 60;
    }

    /**
     * Accessor untuk hari dalam bahasa Indonesia
     */
    public function getHariIndonesiaAttribute()
    {
        $hari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        
        return $hari[$this->hari] ?? $this->hari;
    }

    /**
     * Scope untuk filter by prodi
     */
    public function scopeByProdi($query, $prodiId)
    {
        return $query->where('prodi_id', $prodiId);
    }

    /**
     * Scope untuk filter by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope untuk jadwal aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope untuk filter by hari
     */
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk filter by dosen
     */
    public function scopeByDosen($query, $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    /**
     * Scope untuk filter by ruangan
     */
    public function scopeByRuangan($query, $ruanganId)
    {
        return $query->where('ruangan_id', $ruanganId);
    }

    /**
     * Get jadwal by range waktu
     */
    public function scopeByWaktu($query, $jamMulai, $jamSelesai)
    {
        return $query->where('jam_mulai', '>=', $jamMulai)
                     ->where('jam_selesai', '<=', $jamSelesai);
    }

    /**
     * Helper method untuk menghitung jam selesai dari SKS
     * 
     * @param string $jamMulai
     * @param int $sks
     * @return string
     */
    public static function hitungJamSelesai($jamMulai, $sks)
    {
        // 1 SKS = 50 menit
        $durasi = $sks * 50;
        return date('H:i', strtotime($jamMulai . ' +' . $durasi . ' minutes'));
    }

    /**
     * Check apakah jadwal bentrok dengan jadwal lain
     * 
     * @return bool
     */
    public function isBentrok()
    {
        return self::cekBentrok($this->hari, $this->jam_mulai, $this->jam_selesai, $this->id)
            ->where(function($q) {
                $q->where('dosen_id', $this->dosen_id)
                  ->orWhere('ruangan_id', $this->ruangan_id)
                  ->orWhere(function($q2) {
                      $q2->where('prodi_id', $this->prodi_id)
                         ->where('semester', $this->semester);
                  });
            })
            ->exists();
    }

    /**
     * Get jadwal yang bentrok
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getJadwalBentrok()
    {
        return self::cekBentrok($this->hari, $this->jam_mulai, $this->jam_selesai, $this->id)
            ->where(function($q) {
                $q->where('dosen_id', $this->dosen_id)
                  ->orWhere('ruangan_id', $this->ruangan_id)
                  ->orWhere(function($q2) {
                      $q2->where('prodi_id', $this->prodi_id)
                         ->where('semester', $this->semester);
                  });
            })
            ->with(['mataKuliah', 'dosen', 'ruangan', 'prodi'])
            ->get();
    }

    /**
     * Validasi jadwal sebelum save - DIPERBAIKI dengan validasi mata kuliah duplikat
     * Return array dengan status dan pesan error jika ada
     */
    public function validateJadwal()
    {
        $errors = [];

        // BARU: Cek mata kuliah duplikat di hari yang sama
        $mataKuliahDuplikat = self::cekMataKuliahDuplikat(
            $this->mata_kuliah_id,
            $this->prodi_id,
            $this->semester,
            $this->hari,
            $this->id
        )->with('mataKuliah')->first();

        if ($mataKuliahDuplikat) {
            $errors[] = "Mata kuliah {$mataKuliahDuplikat->mataKuliah->nama_mk} sudah dijadwalkan pada hari {$this->hari} di prodi yang sama. Tidak boleh ada mata kuliah yang sama 2 kali dalam sehari!";
        }

        // Cek bentrok dengan prodi yang sama di semester yang sama
        $bentrokProdi = self::cekBentrokProdi(
            $this->prodi_id,
            $this->semester,
            $this->hari,
            $this->jam_mulai,
            $this->jam_selesai,
            $this->id
        )->with('mataKuliah')->first();

        if ($bentrokProdi) {
            $errors[] = "Bentrok dengan jadwal {$bentrokProdi->mataKuliah->nama_mk} di prodi yang sama pada waktu tersebut";
        }

        // Cek bentrok dosen
        $bentrokDosen = self::cekBentrokDosen(
            $this->dosen_id,
            $this->hari,
            $this->jam_mulai,
            $this->jam_selesai,
            $this->id
        )->with('mataKuliah')->first();

        if ($bentrokDosen) {
            $errors[] = "Dosen sudah mengajar {$bentrokDosen->mataKuliah->nama_mk} pada waktu tersebut";
        }

        // Cek bentrok ruangan
        $bentrokRuangan = self::cekBentrokRuangan(
            $this->ruangan_id,
            $this->hari,
            $this->jam_mulai,
            $this->jam_selesai,
            $this->id
        )->with('mataKuliah')->first();

        if ($bentrokRuangan) {
            $errors[] = "Ruangan sedang digunakan untuk {$bentrokRuangan->mataKuliah->nama_mk} pada waktu tersebut";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}