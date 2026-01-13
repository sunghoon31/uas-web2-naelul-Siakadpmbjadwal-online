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
        // Tambah kolom di tabel calon_mahasiswa untuk status verifikasi
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->enum('status_verifikasi_berkas', [
                'belum_upload',
                'menunggu_verifikasi', 
                'diverifikasi',
                'ditolak'
            ])->default('belum_upload')->after('status_seleksi');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi_berkas');
            $table->string('kartu_ujian')->nullable()->after('catatan_verifikasi');
        });

        // Tabel baru untuk dokumen calon mahasiswa
        Schema::create('dokumen_calon_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')
                  ->constrained('calon_mahasiswa')
                  ->onDelete('cascade');
            
            $table->enum('jenis_dokumen', [
                'ijazah',
                'transkrip_nilai',
                'kartu_keluarga',
                'akta_kelahiran',
                'foto_diri',
                'surat_keterangan_sehat',
                'surat_kelakuan_baik',
                'sertifikat_prestasi',
                'surat_rekomendasi',
                'lainnya'
            ]);
            
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('original_name');
            $table->integer('ukuran_file'); // dalam bytes
            $table->string('mime_type');
            
            $table->enum('status_verifikasi', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');
            
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->timestamps();

            // Indexes
            $table->index('calon_mahasiswa_id');
            $table->index('jenis_dokumen');
            $table->index('status_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_calon_mahasiswa');
        
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi_berkas',
                'catatan_verifikasi',
                'kartu_ujian'
            ]);
        });
    }
};