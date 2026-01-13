<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->string('nidn')->unique();
            $table->string('nama');

            // wajib
            $table->string('email')->unique();
            $table->string('no_hp');

            // optional homebase
            $table->foreignId('prodi_id')
                  ->nullable()
                  ->constrained('prodis')
                  ->nullOnDelete();

            // optional foto
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
