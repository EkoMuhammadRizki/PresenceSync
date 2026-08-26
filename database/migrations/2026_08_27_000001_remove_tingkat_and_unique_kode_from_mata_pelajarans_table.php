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
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            // Drop unique index on kode if exists
            $table->dropUnique('mata_pelajarans_kode_unique');
            
            // Drop tingkat column if exists
            if (Schema::hasColumn('mata_pelajarans', 'tingkat')) {
                $table->dropColumn('tingkat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_pelajarans', 'tingkat')) {
                $table->string('tingkat', 20)->nullable()->after('nama');
            }
            $table->unique('kode', 'mata_pelajarans_kode_unique');
        });
    }
};
