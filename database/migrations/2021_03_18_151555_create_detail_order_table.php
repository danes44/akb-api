<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_order', function (Blueprint $table) {
            $table->id('id_detail');
            $table->integer('jumlah_order')->default(0);
            $table->double('harga_jumlah')->default(0);
            $table->enum('status_order',['sedang dimasak','siap disajikan']);
            $table->time('waktu_order')->useCurrent();
            $table->bigInteger('id_order')->unsigned();
            $table->foreign('id_order')
                ->references('id_order')
                ->on('order')
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
        Schema::dropIfExists('detail_order');
    }
}
