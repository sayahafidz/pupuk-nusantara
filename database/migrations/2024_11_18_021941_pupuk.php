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
        Schema::create('jenis_pupuk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pupuk');
            $table->string('nama_pupuk');
            $table->string('jenis_pupuk');
            $table->string('harga')->nullable();
            $table->string('stok');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupuk');
    }
};
