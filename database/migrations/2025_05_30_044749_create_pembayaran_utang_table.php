<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranUtangTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_utang', function (Blueprint $table) {
            $table->id('id_pembayaran_utang');
            $table->unsignedBigInteger('id_utang');
            $table->unsignedBigInteger('id_pengeluaran');
            $table->unsignedBigInteger('id_rekening');          // tambahkan kolom id_rekening
            $table->double('jumlah_dibayar');
            $table->date('tanggal_pembayaran');
            $table->text('metode_pembayaran')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // foreign key ke tabel utang
            $table->foreign('id_utang')
                  ->references('id_utang')
                  ->on('utang')
                  ->onDelete('cascade');

            // foreign key ke tabel pengeluaran (untuk mencatat kas keluar)
            $table->foreign('id_pengeluaran')
                  ->references('id_pengeluaran')
                  ->on('pengeluaran')
                  ->onDelete('cascade');

            // foreign key ke tabel rekening
            $table->foreign('id_rekening')
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_utang');
    }
}
