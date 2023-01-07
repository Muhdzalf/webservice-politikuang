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
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->char('id_kecamatan', 7)->primary()->unsigned();
            $table->string('nama', 35);
            $table->char('kabupaten_kota_id', 4)->unsigned();
            $table->foreign('kabupaten_kota_id')->references('id_kabupaten_kota')->on('kabupaten_kota')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
};
