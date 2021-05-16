<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColoumnTanggalAndWaktu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stok_keluar', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->dropColumn('waktu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stok_keluar', function (Blueprint $table) {
            $table->date('tanggal');
            $table->time('waktu');
        });
    }
}
