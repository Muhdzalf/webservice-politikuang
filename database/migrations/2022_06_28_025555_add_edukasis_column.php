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
        Schema::table('edukasi', function (Blueprint $table) {
            // relasi dengan tabel user
            $table->bigInteger('user_id')->unsigned()->after('isi');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edukasi', function (Blueprint $table) {
            $table->dropForeign('edukasi_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
};
