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
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id('id_wishlist');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('nama_item', 100);
            $table->string('kategori', 50);
            $table->double('estimasi_harga');
            $table->date('tanggal_target');
            $table->double('dana_terkumpul')->default(0);
            $table->string('sumber_dana', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'tercapai'])->default('pending');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};
