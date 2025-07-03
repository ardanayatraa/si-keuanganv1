<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleTokensToPenggunaTable extends Migration
{
    public function up()
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('google_access_token')->nullable();
            $table->string('google_refresh_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
            ]);
        });
    }
}
