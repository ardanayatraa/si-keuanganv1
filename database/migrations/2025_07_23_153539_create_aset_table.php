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
        Schema::create('aset', function (Blueprint $table) {
            $table->id('id_aset');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('nama_aset', 100);
            $table->enum('jenis_aset', [
                'Tunai/Rekening Bank',
                'Properti',
                'Kendaraan',
                'Elektronik',
                'Investasi',
                'Aset Digital',
                'Lain-lain'
            ]);
            $table->double('nilai_aset');
            $table->date('tanggal_perolehan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'terjual'])->default('aktif');
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
        Schema::dropIfExists('aset');
    }
};
