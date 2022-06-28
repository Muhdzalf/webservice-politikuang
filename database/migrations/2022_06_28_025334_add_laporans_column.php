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
        Schema::table('laporans', function (Blueprint $table) {
            // relasi dengan tabel user
            $table->bigInteger('pengirim_laporan')->unsigned()->after('kronologi_kejadian');
            $table->foreign('pengirim_laporan')->references('id')->on('users');

            //relasi dengan tabel pemilu
            $table->bigInteger('pemilu_id')->unsigned();
            $table->foreign('pemilu_id')->references('id')->on('pemilus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporans', function (Blueprint $table) {
            //drop foreign
            $table->dropForeign('laporans_pengirim_laporan_foreign');
            $table->dropForeign('laporans_pemilu_id_foreign');
            $table->dropColumn(['pengirim_laporan', 'pemilu_id']);
        });
    }
};
