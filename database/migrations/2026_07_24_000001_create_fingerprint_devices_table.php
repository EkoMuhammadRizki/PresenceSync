<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->comment('Nama lokasi device, contoh: Gerbang Utama');
            $table->string('ip_address', 45)->comment('IP Address device, contoh: 192.168.1.201');
            $table->unsignedInteger('port')->default(80)->comment('Port HTTP device (default 80 untuk SOAP)');
            $table->integer('com_key')->default(0)->comment('Comm Key / password koneksi device (0 = tidak ada)');
            $table->string('serial_number', 50)->nullable()->comment('Serial number device dari menu device');
            $table->boolean('is_aktif')->default(true)->comment('Apakah device aktif digunakan');
            $table->timestamp('last_synced_at')->nullable()->comment('Waktu terakhir sync berhasil');
            $table->unsignedInteger('total_synced_logs')->default(0)->comment('Total log yang pernah disync');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_devices');
    }
};
