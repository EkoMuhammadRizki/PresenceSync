<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->string('tahun_lahir_ayah', 4)->nullable()->after('nama_ayah');
            $table->string('tahun_lahir_ibu', 4)->nullable()->after('nama_ibu');
        });
    }

    public function down(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->dropColumn(['tahun_lahir_ayah', 'tahun_lahir_ibu']);
        });
    }
};
