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
        Schema::table('pemilus', function (Blueprint $table) {
            //relasi dengan tabel alamat
            $table->bigInteger('alamat_id')->unsigned()->after('jenis');
            $table->foreign('alamat_id')->references('id')->on('alamats')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemilus', function (Blueprint $table) {
            $table->dropForeign('pemilus_alamat_id_foreign');
            $table->dropColumn('alamat_id');
        });
    }
};
