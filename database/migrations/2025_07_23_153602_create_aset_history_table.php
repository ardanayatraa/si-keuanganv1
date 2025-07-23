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
        Schema::create('aset_history', function (Blueprint $table) {
            $table->id('id_history');
            $table->unsignedBigInteger('id_aset');
            $table->double('nilai_lama');
            $table->double('nilai_baru');
            $table->date('tanggal_perubahan');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_aset')
                  ->references('id_aset')
                  ->on('aset')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_history');
    }
};
