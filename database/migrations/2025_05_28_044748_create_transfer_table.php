<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferTable extends Migration
{
    public function up()
    {
        Schema::create('transfer', function (Blueprint $table) {
            $table->id('id_transfer');
            $table->unsignedBigInteger('id_rekening');
            $table->string('rekening_tujuan', 50);
            $table->double('jumlah');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_rekening')
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer');
    }
}
