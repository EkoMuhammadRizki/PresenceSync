<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('kelas_id')->nullable()->nullOnDelete()->constrained('kelas');
            $table->string('nama', 150);
            $table->string('nisn', 20)->nullable()->unique()->comment('Nomor Induk Siswa Nasional');
            $table->string('nis', 20)->nullable()->unique()->comment('Nomor Induk Siswa lokal');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('no_hp_orang_tua', 20)->nullable();
            $table->string('status', 20)->nullable()->default('aktif');
            $table->string('fingerprint_id', 50)->nullable()->unique()->comment('ID enrollment fingerprint di perangkat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
