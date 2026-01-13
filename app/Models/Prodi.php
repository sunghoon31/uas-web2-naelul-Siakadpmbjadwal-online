<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodis';

    protected $fillable = [
        'nama_prodi',
        'fakultas_id'
    ];

    /**
     * Relasi:
     * Prodi milik satu Fakultas
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    /**
     * Relasi:
     * Prodi memiliki banyak Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'prodi_id');
    }
}
