<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokKeluarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_keluar', function (Blueprint $table) {
            $table->id('id_stok_keluar');
            $table->integer('jumlah')->default(0);
            $table->enum('status',['keluar','sisa']);
            $table->date('tanggal')->useCurrent();
            $table->time('waktu')->useCurrent();
            $table->bigInteger('id_bahan')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_bahan')
                ->references('id_bahan')
                ->on('bahan')
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
        Schema::dropIfExists('stok_keluar');
    }
}
