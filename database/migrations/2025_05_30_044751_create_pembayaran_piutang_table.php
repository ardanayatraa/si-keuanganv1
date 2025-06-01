<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranPiutangTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_piutang', function (Blueprint $table) {
            $table->id('id_pembayaran_piutang');
            $table->unsignedBigInteger('id_piutang');
            $table->unsignedBigInteger('id_pemasukan');
            $table->unsignedBigInteger('id_rekening');        // tambahkan kolom id_rekening
            $table->double('jumlah_dibayar');
            $table->date('tanggal_pembayaran');
            $table->text('metode_pembayaran')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // foreign key ke tabel piutang
            $table->foreign('id_piutang')
                  ->references('id_piutang')
                  ->on('piutang')
                  ->onDelete('cascade');

            // foreign key ke tabel pemasukan (untuk mencatat arus kas masuk saat pelunasan piutang)
            $table->foreign('id_pemasukan')
                  ->references('id_pemasukan')
                  ->on('pemasukan')
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
        Schema::dropIfExists('pembayaran_piutang');
    }
}
