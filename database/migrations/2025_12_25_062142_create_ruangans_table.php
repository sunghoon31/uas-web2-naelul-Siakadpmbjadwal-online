<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->onDelete('cascade');
            $table->string('kode_ruangan');
            $table->string('nama_ruangan');
            $table->integer('kapasitas');
            $table->enum('jenis', ['Kelas', 'Lab', 'Studio'])->default('Kelas');
            $table->timestamps();
            
            // Unique per fakultas
            $table->unique(['fakultas_id', 'kode_ruangan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};