<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekeningTable extends Migration
{
    public function up()
    {
        Schema::create('rekening', function (Blueprint $table) {
            $table->id('id_rekening');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('nama_rekening', 50);
            $table->double('saldo');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekening');
    }
}
