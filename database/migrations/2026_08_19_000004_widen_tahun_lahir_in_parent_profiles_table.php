<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            // Perlebar dari varchar(4) ke varchar(20) agar bisa menampung
            // format tahun (1985), tanggal (1985-01-01), atau variasi lainnya
            $table->string('tahun_lahir_ayah', 20)->nullable()->change();
            $table->string('tahun_lahir_ibu', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->string('tahun_lahir_ayah', 4)->nullable()->change();
            $table->string('tahun_lahir_ibu', 4)->nullable()->change();
        });
    }
};
