<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadiran_mata_pelajarans', function (Blueprint $table) {
            $table->boolean('is_guru_hadir')->default(true)->after('jam_selesai');
            $table->boolean('ada_konfirmasi_guru')->default(false)->after('is_guru_hadir');
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran_mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn(['is_guru_hadir', 'ada_konfirmasi_guru']);
        });
    }
};
