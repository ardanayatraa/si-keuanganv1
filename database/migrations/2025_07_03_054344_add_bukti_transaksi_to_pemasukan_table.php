<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuktiTransaksiToPemasukanTable extends Migration
{
    public function up()
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            // Menambahkan kolom bukti_transaksi untuk menyimpan path file gambar
            $table->string('bukti_transaksi')->nullable()->after('deskripsi');
        });
    }

    public function down()
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->dropColumn('bukti_transaksi');
        });
    }
}
