<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->integer('batas_awal_pulang')->default(0)->after('jam_pulang')->comment('Batas awal (dalam jam setelah jam_masuk) dimana tap berikutnya dianggap sebagai jam pulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->dropColumn('batas_awal_pulang');
        });
    }
};
