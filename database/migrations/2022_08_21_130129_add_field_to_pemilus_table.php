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
            // jenis pemilu fk
            $table->bigInteger('jenis_id')->unsigned()->after('waktu_pelaksanaan')->default(0);
            $table->foreign('jenis_id')->references('id')->on('pemilu')->onUpdate('cascade');
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
            $table->dropForeign('pemilus_jenis_id_foreign');
            $table->dropColumn('jenis_id');
        });
    }
};
