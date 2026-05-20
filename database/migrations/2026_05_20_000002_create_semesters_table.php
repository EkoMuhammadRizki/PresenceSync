<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('jenis', ['ganjil', 'genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            // Satu tahun ajaran hanya boleh punya satu semester ganjil dan satu genap
            $table->unique(['tahun_ajaran_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
