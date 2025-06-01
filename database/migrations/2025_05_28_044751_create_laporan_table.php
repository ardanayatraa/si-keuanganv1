<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanTable extends Migration
{
    public function up()
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_pengguna');
            $table->double('total_pemasukan');
            $table->double('total_pengeluaran');
            $table->double('saldo_akhir');
            $table->string('periode');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan');
    }
}
