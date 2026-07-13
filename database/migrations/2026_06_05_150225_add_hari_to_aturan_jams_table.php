<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])
                  ->nullable()
                  ->after('nama')
                  ->comment('Hari berlakunya aturan jam');
        });
    }

    public function down(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->dropColumn('hari');
        });
    }
};
