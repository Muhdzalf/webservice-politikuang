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
        Schema::table('alamat', function (Blueprint $table) {
            // relasi dengan tabel kecamatan
            $table->char('kecamatan_id', 7)->after('id');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatan')->onDelete('cascade')->onUpdate('cascade');

            // relasi dengan tabel kabupaten
            $table->char('kabupaten_id', 4)->after('kecamatan_id');
            $table->foreign('kabupaten_id')->references('id')->on('kabupaten')->onDelete('cascade')->onUpdate('cascade');

            // relasi dengan tabel kecamatan
            $table->char('provinsi_id', 2)->after('kabupaten_id');
            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alamat', function (Blueprint $table) {
            $table->dropForeign('alamat_kecamatan_id_foreign');
            $table->dropForeign('alamat_kabupaten_id_foreign');
            $table->dropForeign('alamat_provinsi_id_foreign');
            $table->dropColumn(['kecamatan_id', 'kabupaten_id', 'provinsi_id']);
        });
    }
};
