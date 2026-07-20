<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('created_by')->nullable()->constrained('siswas')->nullOnDelete();
            $table->timestamps();

            $table->unique(['mata_pelajaran_id', 'tanggal'], 'unq_mp_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_mata_pelajarans');
    }
};
