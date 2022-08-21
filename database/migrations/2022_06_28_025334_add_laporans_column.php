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
        Schema::table('laporan', function (Blueprint $table) {
            // relasi dengan tabel user
            $table->bigInteger('pengirim_laporan')->unsigned()->after('kronologi_kejadian');
            $table->foreign('pengirim_laporan')->references('id')->on('users');

            //relasi dengan tabel pemilu
            $table->bigInteger('pemilu_id')->unsigned()->after('judul');
            $table->foreign('pemilu_id')->references('id')->on('pemilu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporan', function (Blueprint $table) {
            //drop foreign
            $table->dropForeign('laporan_pengirim_laporan_foreign');
            $table->dropForeign('laporan_pemilu_id_foreign');
            $table->dropColumn(['pengirim_laporan', 'pemilu_id']);
        });
    }
};
