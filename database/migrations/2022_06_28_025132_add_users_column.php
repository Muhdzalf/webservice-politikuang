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
        Schema::table('users', function (Blueprint $table) {
            // menambahkan relasi alamat_id dengan tabel alamat
            $table->bigInteger('alamat_id')->unsigned()->after('nomor_tlp');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_alamat_id_foreign');
            $table->dropColumn('alamat_id');
        });
    }
};
