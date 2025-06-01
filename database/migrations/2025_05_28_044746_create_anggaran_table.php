<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggaranTable extends Migration
{
    public function up()
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id('id_anggaran');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_kategori');
            $table->text('deskripsi')->nullable();
            $table->double('jumlah_batas');
            $table->date('periode_awal');   // e.g. 2025-06-01
            $table->date('periode_akhir');  // e.g. 2025-06-30
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
            // anggaran diasumsikan untuk kategori pengeluaran
            $table->foreign('id_kategori')
                  ->references('id_kategori_pengeluaran')
                  ->on('kategori_pengeluaran')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('anggaran');
    }
}
