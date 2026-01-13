<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calon_mahasiswa', function (Blueprint $table) {
            $table->id();

            // Data Pribadi
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('no_hp', 20);
            
            // Relasi ke Prodi
            $table->foreignId('prodi_id')
                  ->constrained('prodis')
                  ->onDelete('cascade');

            // Data Pendaftaran PMB
            $table->string('no_pendaftaran')->unique();
            $table->enum('jalur_masuk', [
                'reguler',
                'prestasi',
                'beasiswa',
                'pindahan'
            ]);
            $table->string('gelombang')->nullable();
            $table->enum('status_seleksi', [
                'pending',
                'diterima',
                'ditolak'
            ])->default('pending');

            $table->timestamps();

            // Indexes untuk optimasi query
            $table->index('status_seleksi');
            $table->index('jalur_masuk');
            $table->index(['prodi_id', 'status_seleksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_mahasiswa');
    }
};