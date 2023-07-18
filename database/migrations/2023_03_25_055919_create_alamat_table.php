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
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');

            // foreign key to provinsi table
            $table->char('provinsi_id', 2);
            $table->foreign('provinsi_id')->references('id_provinsi')->on('provinsi')->onUpdate('cascade')->cascadeOnDelete();

            // foreign key to kabupaten kota table
            $table->char('kabupaten_kota_id', 4);
            $table->foreign('kabupaten_kota_id')->references('id_kabupaten_kota')->on('kabupaten_kota')->onUpdate('cascade')->cascadeOnDelete();

            // foreign key to kecamatan table
            $table->char('kecamatan_id', 7);
            $table->foreign('kecamatan_id')->references('id_kecamatan')->on('kecamatan')->onUpdate('cascade')->cascadeOnDelete();

            $table->string('desa', 35);
            $table->string('detail_alamat', 50);
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
        Schema::dropIfExists('alamat');
    }
};
