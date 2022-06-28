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
        Schema::table('kecamatans', function (Blueprint $table) {
            //relasi dengan tabel kabupaten
            $table->bigInteger('kabupaten_id')->unsigned()->after('nama');
            $table->foreign('kabupaten_id')->references('id')->on('kabupatens')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kecamatans', function (Blueprint $table) {
            $table->dropForeign('kecamatans_kabupaten_id_foreign');
            $table->dropColumn('kabupaten_id');
        });
    }
};
