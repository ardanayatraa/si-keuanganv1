<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaranTable extends Migration
{
    public function up()
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id('id_pengeluaran');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_rekening');
            $table->double('jumlah');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
            $table->foreign('id_rekening')
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('cascade');
            $table->foreign('id_kategori')
                  ->references('id_kategori_pengeluaran')
                  ->on('kategori_pengeluaran')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengeluaran');
    }
}
