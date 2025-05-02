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
        Schema::create('master_data_tbm', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('regional');
            $table->string('kode_kebun');
            $table->string('nama_kebun');
            $table->string('afdeling');
            $table->string('blok');
            $table->integer('tahun_tanam');
            $table->integer('bulan_tanam');
            $table->string('luas_ha');
            $table->integer('jumlah_pohon');
            $table->string('bahan_tanam');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_tbm');
    }
};
