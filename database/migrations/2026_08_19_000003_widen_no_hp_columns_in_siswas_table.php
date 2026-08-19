<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Perlebar kolom no_hp dan no_hp_orang_tua dari varchar(20) ke varchar(30)
            // agar bisa menampung format nomor HP dengan kode negara, spasi, atau tanda hubung
            $table->string('no_hp', 30)->nullable()->change();
            $table->string('no_hp_orang_tua', 30)->nullable()->change();
            // Perlebar nik dari varchar(20) ke varchar(20) — sudah cukup, tapi nik siswa bisa 16 digit
            // Amankan juga kolom nik untuk konsistensi
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->change();
            $table->string('no_hp_orang_tua', 20)->nullable()->change();
        });
    }
};
