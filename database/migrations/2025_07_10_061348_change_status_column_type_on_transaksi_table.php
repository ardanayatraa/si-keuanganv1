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
        $table->string('status')->default('belum lunas')->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utang', function (Blueprint $table) {
        $table->enum('status', ['lunas', 'belum lunas'])->default('belum lunas')->change();
    });
    }
};
