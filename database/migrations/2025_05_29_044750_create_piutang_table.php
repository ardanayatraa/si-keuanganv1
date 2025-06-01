<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiutangTable extends Migration
{
    public function up()
    {
        Schema::create('piutang', function (Blueprint $table) {
            $table->id('id_piutang');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_rekening');          // tambahkan id_rekening
            $table->unsignedBigInteger('id_pemasukan')->nullable(); // biarkan null sampai pelunasan
            $table->double('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');                // ubah jadi date
            $table->text('deskripsi')->nullable();
            $table->double('sisa_piutang')->nullable();         // inisialisasi sisa_piutang = jumlah
            $table->string('status')->default('belum lunas');   // inisialisasi status
            $table->timestamps();

            // FK: pengguna
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');

            // FK: rekening (dari mana uang dipinjamkan)
            $table->foreign('id_rekening')
                  ->references('id_rekening')
                  ->on('rekening')
                  ->onDelete('restrict');

            // FK: pemasukan (dihubungkan saat pelunasan)
            $table->foreign('id_pemasukan')
                  ->references('id_pemasukan')
                  ->on('pemasukan')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('piutang');
    }
}
