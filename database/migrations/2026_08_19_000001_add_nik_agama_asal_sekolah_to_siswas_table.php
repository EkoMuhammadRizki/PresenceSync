<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('nis')->comment('Nomor Induk Kependudukan siswa');
            $table->string('agama', 50)->nullable()->after('tempat_lahir');
            $table->string('asal_sekolah', 150)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nik', 'agama', 'asal_sekolah']);
        });
    }
};
