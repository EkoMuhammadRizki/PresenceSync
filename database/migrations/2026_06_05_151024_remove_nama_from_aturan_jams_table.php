<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->dropColumn('nama');
        });
    }

    public function down(): void
    {
        Schema::table('aturan_jams', function (Blueprint $table) {
            $table->string('nama', 100)->after('id')->nullable();
        });
    }
};
