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
            $table->dropForeign(['id_master_data']);
            $table->dropColumn('id_master_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemupukan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_master_data');
            $table->foreign('id_master_data')->references('id')->on('master_data');
        });
    }
};
