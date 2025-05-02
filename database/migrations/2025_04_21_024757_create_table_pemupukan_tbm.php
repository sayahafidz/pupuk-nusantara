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
        Schema::create('pemupukan_tbm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pupuk');
            $table->unsignedBigInteger('id_master_data_tbm');
            $table->string('regional');
            $table->string('kebun');
            $table->string('afdeling');
            $table->string('blok');
            $table->string('tahun_tanam');
            $table->string('luas_blok');
            $table->string('jumlah_pokok');
            $table->string('bulan_tanam');
            $table->string('bahan_tanam');
            $table->string('jenis_pupuk');
            $table->string('jumlah_pupuk');
            $table->string('luas_pemupukan');
            $table->string('tgl_pemupukan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pupuk')->references('id')->on('jenis_pupuk');
            $table->foreign('id_master_data_tbm')->references('id')->on('master_data_tbm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemupukan_tbm');
    }
};
