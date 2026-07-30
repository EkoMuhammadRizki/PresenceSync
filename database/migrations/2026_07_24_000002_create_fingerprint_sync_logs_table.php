<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fingerprint_device_id')->constrained('fingerprint_devices')->cascadeOnDelete();
            $table->string('fingerprint_uid', 50)->comment('User ID / PIN di device saat scan');
            $table->dateTime('scan_time')->comment('Waktu scan dari device');
            $table->tinyInteger('verified')->default(0)->comment('Mode verifikasi: 0=Finger, 1=PIN, dll');
            $table->tinyInteger('status')->default(0)->comment('Status dari device: 0=Check-In, 1=Check-Out, dll');
            $table->boolean('is_processed')->default(false)->comment('Sudah diproses jadi data kehadiran?');
            $table->foreignId('kehadiran_id')->nullable()->constrained('kehadirans')->nullOnDelete();
            $table->text('error_note')->nullable()->comment('Catatan error jika gagal diproses');
            $table->timestamps();

            // Composite unique key: satu siswa satu scan per waktu per device
            $table->unique(['fingerprint_device_id', 'fingerprint_uid', 'scan_time'], 'unique_scan_per_device');
            $table->index(['is_processed', 'scan_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_sync_logs');
    }
};
