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
        Schema::create('alamat_pemilu', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->unsignedBigInteger('provinsi_id')->nullable();
            $table->foreign('provinsi_id')->references('id_provinsi')->on('provinsi')->onUpdate('cascade')->nullOnDelete();
            $table->unsignedBigInteger('kabupaten_kota_id')->nullable();
            $table->foreign('kabupaten_kota_id')->references('id_kabupaten_kota')->on('kabupaten_kota')->onUpdate('cascade')->nullOnDelete();
            $table->unsignedBigInteger('kecamatan_id')->nullable();
            $table->foreign('kecamatan_id')->references('id_kecamatan')->on('kecamatan')->onUpdate('cascade')->nullOnDelete();;
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
        Schema::dropIfExists('alamat_pemilu');
    }
};
