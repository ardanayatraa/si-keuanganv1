<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('utang', function (Blueprint $table) {
            $table->integer('jangka_waktu_bulan')->nullable()->after('tanggal_jatuh_tempo');
            $table->double('jumlah_cicilan_per_bulan')->nullable()->after('jangka_waktu_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utang', function (Blueprint $table) {
            $table->dropColumn(['jangka_waktu_bulan', 'jumlah_cicilan_per_bulan']);
        });
    }
};
