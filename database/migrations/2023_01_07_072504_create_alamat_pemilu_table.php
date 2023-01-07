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

            $table->char('provinsi_id', 2)->unsigned()->nullable();
            $table->foreign('provinsi_id')->references('id_provinsi')->on('provinsi')->onUpdate('cascade')->nullOnDelete();

            $table->char('kabupaten_kota_id', 4)->nullable()->unsigned();
            $table->foreign('kabupaten_kota_id')->references('id_kabupaten_kota')->on('kabupaten_kota')->onUpdate('cascade')->nullOnDelete();

            $table->char('kecamatan_id', 7)->nullable()->unsigned();
            $table->foreign('kecamatan_id')->references('id_kecamatan')->on('kecamatan')->onUpdate('cascade')->nullOnDelete();

            $table->string('desa', 35);
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
