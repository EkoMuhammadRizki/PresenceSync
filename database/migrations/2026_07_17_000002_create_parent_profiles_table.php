<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->onDelete('cascade');
            
            // Ayah
            $table->string('nik_ayah')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('ket_pekerjaan_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('alamat_ayah')->nullable();
            $table->string('no_hp_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();

            // Ibu
            $table->string('nik_ibu')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('ket_pekerjaan_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('alamat_ibu')->nullable();
            $table->string('no_hp_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parent_profiles');
    }
};
