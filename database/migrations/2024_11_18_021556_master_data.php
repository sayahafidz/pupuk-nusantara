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
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('kondisi');
            $table->string('status_umur');
            $table->string('kode_kebun');
            $table->string('nama_kebun');
            $table->string('kkl_kebun');
            $table->string('afdeling');
            $table->string('tahun_tanam');
            $table->string('no_blok');
            $table->string('luas');
            $table->string('jlh_pokok');
            $table->string('pkk_ha');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
