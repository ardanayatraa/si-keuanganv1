<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemasukanTable extends Migration
{
    public function up()
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id('id_pemasukan');
            $table->double('jumlah');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_rekening');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('id_pengguna');

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
            $table->foreign('id_rekening')
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('cascade');
            $table->foreign('id_kategori')
                  ->references('id_kategori_pemasukan')
                  ->on('kategori_pemasukan')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemasukan');
    }
}
