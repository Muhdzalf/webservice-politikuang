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
        Schema::table('kabupatens', function (Blueprint $table) {
            //relasi dengan tabel provinsi
            $table->char('provinsi_id', 2)->after('nama');
            $table->foreign('provinsi_id')->references('id')->on('provinsis')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kabupatens', function (Blueprint $table) {
            $table->dropForeign('kabupatens_provinsi_id_foreign');
            $table->dropColumn('provinsi_id');
        });
    }
};
