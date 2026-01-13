<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->year('angkatan')->default(2020);
            $table->unsignedBigInteger('prodi_id');
            $table->string('foto')->nullable();
            $table->timestamps();
            
            $table->foreign('prodi_id')
                  ->references('id')
                  ->on('prodis')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};