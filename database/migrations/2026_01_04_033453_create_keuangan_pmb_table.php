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
        Schema::create('keuangan_pmb', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Calon Mahasiswa
            $table->foreignId('calon_mahasiswa_id')
                  ->constrained('calon_mahasiswa')
                  ->onDelete('cascade');
            
            // Jenis Biaya PMB
            $table->enum('jenis_biaya', [
                'formulir',
                'ujian',
                'daftar_ulang'
            ]);
            
            // Detail Biaya
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            
            // Status Pembayaran
            $table->enum('status_bayar', [
                'belum_bayar',
                'sudah_bayar',
                'dibebaskan'
            ])->default('belum_bayar');
            
            // Info Pembayaran
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_bayar')->nullable(); // Transfer, Cash, VA, dll
            $table->string('bukti_bayar')->nullable(); // path file bukti
            
            $table->timestamps();
            
            // Indexes
            $table->index(['calon_mahasiswa_id', 'jenis_biaya']);
            $table->index('status_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_pmb');
    }
};