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
        Schema::table('kecamatan', function (Blueprint $table) {
            //relasi dengan tabel kabupaten
            $table->char('kabupaten_id', 4)->after('nama');
            $table->foreign('kabupaten_id')->references('id')->on('kabupaten')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kecamatan', function (Blueprint $table) {
            $table->dropForeign('kecamatan_kabupaten_id_foreign');
            $table->dropColumn('kabupaten_id');
        });
    }
};
