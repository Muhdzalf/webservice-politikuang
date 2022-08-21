<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_laporans', function (Blueprint $table) {
            // relasi dengan tabel laporan
            $table->bigInteger('laporan_id')->unsigned()->after('id');
            $table->foreign('laporan_id')->references('id')->on('laporans');

            // relasi dengan tabel user
            $table->bigInteger('user_id')->unsigned()->after('id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('progress_laporans', function (Blueprint $table) {
            $table->dropForeign('progress_laporans_laporan_id_foreign');
            $table->dropForeign('progress_laporans_user_id_foreign');
            $table->dropColumn(['laporan_id', 'user_id']);
        });
    }
};
