<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtangTable extends Migration
{
    public function up()
    {
        Schema::create('utang', function (Blueprint $table) {
            $table->id('id_utang');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_rekening');        // tambah id_rekening
            $table->double('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');

            $table->foreign('id_rekening')                    // relasi ke tabel rekening
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('utang');
    }
}
