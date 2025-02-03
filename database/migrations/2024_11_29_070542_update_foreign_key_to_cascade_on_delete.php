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
            // Drop the existing foreign key
            $table->dropForeign(['id_pupuk']);

            // Add the foreign key back with ON DELETE CASCADE
            $table->foreign('id_pupuk')
                ->references('id')
                ->on('jenis_pupuk')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemupukan', function (Blueprint $table) {
            // Drop the cascading foreign key
            $table->dropForeign(['id_pupuk']);

            // Add the foreign key back without cascading
            $table->foreign('id_pupuk')
                ->references('id')
                ->on('jenis_pupuk')
                ->onDelete('restrict');
        });
    }
};
