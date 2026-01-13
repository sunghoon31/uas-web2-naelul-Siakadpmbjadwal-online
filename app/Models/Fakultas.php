<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'nama_fakultas'
    ];

    /**
     * Relasi:
     * Fakultas memiliki banyak Prodi
     */
    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'fakultas_id');
    }
}
