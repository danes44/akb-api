<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id("id_pegawai");
            $table->bigInteger("id_role")->unsigned();
            $table->string("nama_pegawai");
            $table->enum("jenis_kelamin",['pria','wanita']);
            $table->dateTime("tgl_gabung");
            $table->dateTime("tgl_keluar")->nullable();
            $table->enum("status_pegawai",['aktif','non aktif'])->default('aktif');
            $table->string("email")->unique();
            $table->string("password");
            $table->timestamps();
            $table->foreign('id_role')
                ->references('id_role')
                ->on('role')
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
        Schema::dropIfExists('pegawai');
    }
}
