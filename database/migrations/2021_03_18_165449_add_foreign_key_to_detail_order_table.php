<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToDetailOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_order', function (Blueprint $table) {
            $table->bigInteger('id_menu')->unsigned();
            $table->foreign('id_menu')
                ->references('id_menu')
                ->on('menu')
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
        Schema::table('detail_order', function (Blueprint $table) {
            //
        });
    }
}
