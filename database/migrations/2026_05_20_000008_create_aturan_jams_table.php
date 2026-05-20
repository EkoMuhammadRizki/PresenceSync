<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_jams', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->comment('Contoh: Jadwal Normal, Jadwal Ramadhan');
            $table->time('jam_masuk');
            $table->unsignedSmallInteger('toleransi_keterlambatan')->default(15)->comment('Dalam menit');
            $table->time('jam_pulang');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_jams');
    }
};
