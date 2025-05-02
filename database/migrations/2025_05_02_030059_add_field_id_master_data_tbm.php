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
        if (!Schema::hasColumn('rencana_pemupukan_tbm', 'id_master_data_tbm')) {
            Schema::table('rencana_pemupukan_tbm', function (Blueprint $table) {
                $table->unsignedBigInteger('id_master_data_tbm')->after('id_pupuk')->nullable();
                $table->foreign('id_master_data_tbm')->references('id')->on('master_data_tbm');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('rencana_pemupukan_tbm', 'id_master_data_tbm')) {
            Schema::table('rencana_pemupukan_tbm', function (Blueprint $table) {
                $table->dropForeign(['id_master_data_tbm']); // Use array here
                $table->dropColumn('id_master_data_tbm');
            });
        }
    }
};
