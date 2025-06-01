<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriPemasukanTable extends Migration
{
    public function up()
    {
        Schema::create('kategori_pemasukan', function (Blueprint $table) {
            $table->id('id_kategori_pemasukan');
            $table->text('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->string('icon', 100)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('id_pengguna');
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_pemasukan');
    }
}
