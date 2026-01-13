<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('semester');
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['hari', 'jam_mulai', 'jam_selesai']);
            $table->index(['dosen_id', 'hari']);
            $table->index(['ruangan_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};