<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuktiTransaksiToPengeluaranTable extends Migration
{
    public function up()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('bukti_transaksi')->nullable()->after('deskripsi');
        });
    }

    public function down()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('bukti_transaksi');
        });
    }
}
