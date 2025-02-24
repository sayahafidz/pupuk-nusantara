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
        Schema::table('pemupukan', function (Blueprint $table) {
            $table->string('cara_pemupukan')->nullable();
            $table->string('jumlah_mekanisasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemupukan', function (Blueprint $table) {
            $table->dropColumn('cara_pemupukan');
            $table->dropColumn('jumlah_mekanisasi');
        });
    }
};
