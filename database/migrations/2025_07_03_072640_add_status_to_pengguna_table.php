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
       Schema::table('pengguna', function (Blueprint $table) {
        // Jika ingin pakai enum:
        $table->enum('status', ['aktif', 'nonaktif'])
              ->default('aktif')
              ->after('foto');

        // Atau pakai boolean:
        // $table->boolean('is_active')->default(true)->after('foto');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('pengguna', function (Blueprint $table) {
        $table->dropColumn('status');
        // $table->dropColumn('is_active');
    });
    }
};
