<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('jenis_kelamin', 10)->nullable()->after('nama');
            $table->string('tempat_lahir', 100)->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 50)->nullable()->after('tanggal_lahir');
            $table->string('status', 50)->default('aktif')->after('alamat');
            $table->string('nik', 20)->nullable()->after('status');
            $table->string('npwp', 30)->nullable()->after('nik');
            $table->string('nuptk', 30)->nullable()->after('npwp');
            $table->string('status_kepegawaian', 100)->nullable()->after('nuptk');
            $table->string('tugas_tambahan', 150)->nullable()->after('status_kepegawaian');
            $table->string('sk_cpns', 100)->nullable()->after('tugas_tambahan');
            $table->date('tanggal_cpns')->nullable()->after('sk_cpns');
            $table->string('sk_pengangkatan', 100)->nullable()->after('tanggal_cpns');
            $table->date('tmt_pengangkatan')->nullable()->after('sk_pengangkatan');
            $table->string('lembaga_pengangkatan', 150)->nullable()->after('tmt_pengangkatan');
            $table->string('pangkat_golongan', 50)->nullable()->after('lembaga_pengangkatan');
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'status',
                'nik',
                'npwp',
                'nuptk',
                'status_kepegawaian',
                'tugas_tambahan',
                'sk_cpns',
                'tanggal_cpns',
                'sk_pengangkatan',
                'tmt_pengangkatan',
                'lembaga_pengangkatan',
                'pangkat_golongan',
            ]);
        });
    }
};
