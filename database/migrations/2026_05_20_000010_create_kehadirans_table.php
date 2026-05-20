<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('aturan_jam_id')->nullable()->nullOnDelete()->constrained('aturan_jams');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu siswa hanya satu record per tanggal
            $table->unique(['siswa_id', 'tanggal']);
            $table->index(['tanggal', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
