<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->enum('source', ['manual', 'fingerprint'])->default('manual')
                ->after('keterangan')
                ->comment('Sumber data kehadiran: manual (input user) atau fingerprint (otomatis dari device)');
            $table->foreignId('fingerprint_log_id')->nullable()
                ->after('source')
                ->constrained('fingerprint_sync_logs')
                ->nullOnDelete()
                ->comment('Referensi ke log mentah fingerprint jika source=fingerprint');
            // Tambahkan guru_id nullable untuk mendukung kehadiran guru dari fingerprint di masa depan
            $table->foreignId('guru_id')->nullable()
                ->after('fingerprint_log_id')
                ->constrained('gurus')
                ->nullOnDelete()
                ->comment('ID guru jika kehadiran ini milik guru (bukan siswa)');
        });
    }

    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropForeign(['fingerprint_log_id']);
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['source', 'fingerprint_log_id', 'guru_id']);
        });
    }
};
