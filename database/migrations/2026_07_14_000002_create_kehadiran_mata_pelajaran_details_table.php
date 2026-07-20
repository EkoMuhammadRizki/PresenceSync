<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_mata_pelajaran_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kehadiran_mata_pelajaran_id');
            $table->unsignedBigInteger('siswa_id');
            $table->foreign('kehadiran_mata_pelajaran_id', 'fk_kmp_header')
                  ->references('id')->on('kehadiran_mata_pelajarans')->cascadeOnDelete();
            $table->foreign('siswa_id', 'fk_kmp_siswa')
                  ->references('id')->on('siswas')->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['kehadiran_mata_pelajaran_id', 'siswa_id'], 'unq_kmp_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_mata_pelajaran_details');
    }
};
