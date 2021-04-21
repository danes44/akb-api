<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id('id_reservasi');
            $table->bigInteger('id_customer')->unsigned();
            $table->bigInteger('no_meja')->unsigned();
            $table->dateTime('tgl_reservasi');
            $table->enum('sesi',["lunch","dinner"]);
            $table->bigInteger('id_waiter')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_customer')
                ->references('id_customer')
                ->on('customer')
                ->onDelete('CASCADE');
            $table->foreign('no_meja')
                ->references('no_meja')
                ->on('meja')
                ->onDelete('CASCADE');
            $table->foreign('id_waiter')
                ->references('id_pegawai')
                ->on('pegawai')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservasi');
    }
}
