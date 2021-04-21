<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokMasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_masuk', function (Blueprint $table) {
            $table->id('id_stok_masuk');
            $table->integer('jumlah')->default(0);
            $table->double('harga');
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
        Schema::dropIfExists('stok_masuk');
    }
}
