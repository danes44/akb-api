<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('no_transaksi'); //no transaksi dari semua transaksi di semua waktu
            $table->string('id_transaksi'); //id transaksi yang jadi nomor struk
            $table->enum('payment_method',['cash','credit/debit card']);
            $table->bigInteger('id_kartu')->unsigned()->nullable();
            $table->string('no_verifikasi')->nullable();
            $table->bigInteger('id_order')->unsigned();
            $table->bigInteger('id_kasir')->unsigned();
            $table->double('subtotal');
            $table->double('service');
            $table->double('tax');
            $table->dateTime('waktu_transaksi')->useCurrent();
            $table->double('total');
            $table->foreign('id_kartu')
                ->references('id_kartu')
                ->on('kartu')
                ->onDelete('CASCADE');
            $table->foreign('id_order')
                ->references('id_order')
                ->on('order')
                ->onDelete('CASCADE');
            $table->foreign('id_kasir')
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
        Schema::dropIfExists('transaksi');
    }
}
