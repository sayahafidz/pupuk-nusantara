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
        // create new database table rencana_pemupukan
        Schema::create('rencana_pemupukan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pupuk');
            $table->string('regional');
            $table->string('kebun');
            $table->string('afdeling');
            $table->string('blok');
            $table->string('tahun_tanam');
            $table->string('luas_blok');
            $table->string('jumlah_pokok');
            $table->string('jenis_pupuk');
            $table->string('jumlah_pupuk');
            $table->string('luas_pemupukan');
            $table->string('semester_pemupukan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_pemupukan');
    }
};
