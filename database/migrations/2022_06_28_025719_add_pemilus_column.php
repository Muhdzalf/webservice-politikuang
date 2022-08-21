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
        Schema::table('pemilu', function (Blueprint $table) {
            //relasi dengan tabel alamat
            $table->bigInteger('alamat_id')->unsigned()->after('waktu_pelaksanaan');
            $table->foreign('alamat_id')->references('id')->on('alamat')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemilu', function (Blueprint $table) {
            $table->dropForeign('pemilu_alamat_id_foreign');
            $table->dropColumn('alamat_id');
        });
    }
};
