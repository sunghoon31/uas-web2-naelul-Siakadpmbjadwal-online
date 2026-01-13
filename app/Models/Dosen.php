<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'nidn',
        'nama',
        'email',
        'no_hp',
        'prodi_id',
        'foto',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}
