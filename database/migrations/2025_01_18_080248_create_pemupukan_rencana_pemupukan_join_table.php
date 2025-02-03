<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemupukanRencanaPemupukanJoinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemupukan_rencana_pemupukan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemupukan_id'); // Foreign key for pemupukan
            $table->unsignedBigInteger('rencana_pemupukan_id'); // Foreign key for rencana_pemupukan
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('pemupukan_id')->references('id')->on('pemupukan')->onDelete('cascade');
            $table->foreign('rencana_pemupukan_id')->references('id')->on('rencana_pemupukan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemupukan_rencana_pemupukan');
    }
}
