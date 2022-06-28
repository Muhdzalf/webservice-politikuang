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
        Schema::table('alamats', function (Blueprint $table) {
            // relasi dengan tabel kecamatan
            $table->bigInteger('kecamatan_id')->unsigned()->after('id');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatans');

            // relasi dengan tabel kabupaten
            $table->bigInteger('kabupaten_id')->unsigned();
            $table->foreign('kabupaten_id')->references('id')->on('kabupatens');

            // relasi dengan tabel kecamatan
            $table->bigInteger('provinsi_id')->unsigned();
            $table->foreign('provinsi_id')->references('id')->on('provinsis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alamats', function (Blueprint $table) {
            $table->dropForeign('alamats_kecamatan_id_foreign');
            $table->dropForeign('alamats_kabupaten_id_foreign');
            $table->dropForeign('alamats_provinsi_id_foreign');
            $table->dropColumn(['kecamatan_id', 'kabupaten_id', 'provinsi_id']);
        });
    }
};
