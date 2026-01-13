<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fakultas_id',
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'jenis'
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}